<?php

/**
 * @file: TireServiceController.php
 * @description: Веб-контроллер для отображения страниц шиномонтажных сервисов
 * @dependencies: TireServiceRepository, Inertia
 * @created: 2024-01-01
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\TireService\Repositories\TireServiceRepository;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Domain\TireService\Models\TireService;

class TireServiceController extends Controller
{
    public function __construct(
        private TireServiceRepository $tireServiceRepository
    ) {}

    /**
     * Главная страница с поиском и фильтрацией
     */
    public function index(Request $request): Response
    {
        // Получаем общее количество записей для заголовка
        $totalCount = TireService::count();
        
        // Get filter statistics
        $filterStats = [
            'rooms_counts' => TireService::distinct('rooms_count')->orderBy('rooms_count')->pluck('rooms_count')->toArray(),
            'area_range' => [
                'min' => TireService::min('area'),
                'max' => TireService::max('area')
            ]
        ];

        // Build query
        $query = TireService::query();

        // Apply filters
        $hasNameFilter = $request->filled('name');
        if ($hasNameFilter) {
            $name = $request->get('name');
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($request->boolean('has_image')) {
            $query->whereNotNull('image');
        }

        if ($request->filled('rooms_count')) {
            $roomsCounts = $request->get('rooms_count');
            if (is_array($roomsCounts)) {
                $query->whereIn('rooms_count', $roomsCounts);
            }
        }

        if ($request->filled('area_min')) {
            $query->where('area', '>=', $request->get('area_min'));
        }

        if ($request->filled('area_max')) {
            $query->where('area', '<=', $request->get('area_max'));
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'desc');

        if ($hasNameFilter) {
            // При поиске по названию - сортировка по релевантности
            $name = $request->get('name');
            $query->orderByRaw("
                CASE 
                    WHEN LOWER(name) = LOWER(?) THEN 1
                    WHEN LOWER(name) LIKE LOWER(?) THEN 2
                    WHEN LOWER(name) LIKE LOWER(?) THEN 3
                    ELSE 4
                END
            ", [$name, $name . '%', '%' . $name . '%']);
            
            // Дополнительная сортировка внутри каждой группы релевантности
            if ($sortBy === 'name') {
                $query->orderBy('name', $sortDirection);
            } elseif ($sortBy === 'area') {
                $query->orderBy('area', $sortDirection);
            } elseif ($sortBy === 'rooms_count') {
                $query->orderBy('rooms_count', $sortDirection);
            } else {
                $query->orderBy('id', $sortDirection);
            }
        } else {
            // Обычная сортировка без поиска по названию
            if ($sortBy === 'name') {
                $query->orderBy('name', $sortDirection);
            } elseif ($sortBy === 'area') {
                $query->orderBy('area', $sortDirection);
            } elseif ($sortBy === 'rooms_count') {
                $query->orderBy('rooms_count', $sortDirection);
            } elseif ($sortBy === 'created_at') {
                $query->orderBy('created_at', $sortDirection);
            } else {
                $query->orderBy('id', $sortDirection);
            }
        }

        // Get results with pagination (20 записей для полного заполнения строк)
        $tireServices = $query->paginate(20);

        return Inertia::render('TireServices/Index', [
            'initialData' => [
                'data' => $tireServices->items(),
                'pagination' => [
                    'total' => $tireServices->total(),
                    'per_page' => $tireServices->perPage(),
                    'current_page' => $tireServices->currentPage(),
                    'last_page' => $tireServices->lastPage(),
                    'from' => $tireServices->firstItem(),
                    'to' => $tireServices->lastItem(),
                ],
                'total_count' => $totalCount,
                'sorting' => [
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                    'has_name_filter' => $hasNameFilter
                ]
            ],
            'filterStats' => $filterStats,
            'initialFilters' => [
                'name' => $request->get('name', ''),
                'has_image' => $request->boolean('has_image'),
                'rooms_count' => $request->get('rooms_count', []),
                'area_min' => $request->get('area_min'),
                'area_max' => $request->get('area_max'),
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection,
            ]
        ]);
    }

    /**
     * Страница детальной информации о сервисе
     */
    public function show(int $id): Response
    {
        $tireService = $this->tireServiceRepository->findById($id);

        if (!$tireService) {
            abort(404, 'Шиномонтажный сервис не найден');
        }

        return Inertia::render('TireServices/Show', [
            'tireService' => $tireService,
        ]);
    }
}
