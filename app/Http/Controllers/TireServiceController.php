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
        // Получаем статистику для фильтров
        $filterStats = $this->tireServiceRepository->getFilterStats();

        // Начальные данные (первая страница)
        $initialFilters = [
            'name' => $request->get('name'),
            'has_image' => $request->boolean('has_image'),
            'rooms_count' => $request->get('rooms_count', []),
            'area_min' => $request->get('area_min'),
            'area_max' => $request->get('area_max'),
        ];

        // Фильтруем пустые значения
        $initialFilters = array_filter($initialFilters, fn($value) => !empty($value));

        $initialData = $this->tireServiceRepository->searchWithFilters($initialFilters, 15);

        return Inertia::render('TireServices/Index', [
            'initialData' => [
                'data' => $initialData->items(),
                'pagination' => [
                    'current_page' => $initialData->currentPage(),
                    'last_page' => $initialData->lastPage(),
                    'per_page' => $initialData->perPage(),
                    'total' => $initialData->total(),
                    'from' => $initialData->firstItem(),
                    'to' => $initialData->lastItem(),
                ],
            ],
            'filterStats' => $filterStats,
            'initialFilters' => $initialFilters,
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
