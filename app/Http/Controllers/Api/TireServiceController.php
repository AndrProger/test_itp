<?php

/**
 * @file: TireServiceController.php
 * @description: API контроллер для работы с шиномонтажными сервисами
 * @dependencies: TireServiceRepository, Inertia
 * @created: 2024-01-01
 */

namespace App\Http\Controllers\Api;

use App\Domain\TireService\Repositories\TireServiceRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Domain\TireService\Models\TireService;

class TireServiceController extends Controller
{
    public function __construct(
        private TireServiceRepository $tireServiceRepository
    ) {}

    /**
     * Поиск и фильтрация сервисов
     */
    public function index(Request $request): JsonResponse
    {
        // Получаем общее количество записей для заголовка
        $totalCount = TireService::count();
        
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

        // Get per_page from request, default to 20
        $perPage = $request->get('per_page', 20);

        // Get results with pagination
        $tireServices = $query->paginate($perPage);

        return response()->json([
            'success' => true,
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
        ]);
    }

    /**
     * Получить статистику для фильтров
     */
    public function stats(): JsonResponse
    {
        $stats = $this->tireServiceRepository->getFilterStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Показать конкретный сервис
     */
    public function show(int $id): JsonResponse
    {
        $tireService = TireService::find($id);

        if (!$tireService) {
            return response()->json([
                'success' => false,
                'message' => 'Шиномонтажный сервис не найден'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tireService
        ]);
    }

    /**
     * Создать новый сервис
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'rooms_count' => 'required|integer|min:1|max:10',
            'floor' => 'required|integer|min:1|max:20',
            'area' => 'required|numeric|min:1|max:1000',
            'description' => 'nullable|string|max:1000',
        ]);

        $tireService = $this->tireServiceRepository->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Шиномонтажный сервис успешно создан',
            'data' => $tireService,
        ], 201);
    }

    /**
     * Обновить существующий сервис
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'image' => 'nullable|string|max:255',
            'rooms_count' => 'sometimes|required|integer|min:1|max:10',
            'floor' => 'sometimes|required|integer|min:1|max:20',
            'area' => 'sometimes|required|numeric|min:1|max:1000',
            'description' => 'nullable|string|max:1000',
        ]);

        $tireService = $this->tireServiceRepository->update($id, $validated);

        if (!$tireService) {
            return response()->json([
                'success' => false,
                'message' => 'Шиномонтажный сервис не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Шиномонтажный сервис успешно обновлен',
            'data' => $tireService,
        ]);
    }

    /**
     * Удалить сервис
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->tireServiceRepository->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Шиномонтажный сервис не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Шиномонтажный сервис успешно удален',
        ]);
    }
}
