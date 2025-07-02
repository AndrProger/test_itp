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

class TireServiceController extends Controller
{
    public function __construct(
        private TireServiceRepository $tireServiceRepository
    ) {}

    /**
     * Поиск и фильтрация шиномонтажных сервисов
     */
    public function index(Request $request): JsonResponse
    {
        // Simplified validation for debugging
        $name = $request->get('name');
        $hasImage = $request->get('has_image');
        $roomsCount = $request->get('rooms_count', []);
        $areaMin = $request->get('area_min');
        $areaMax = $request->get('area_max');
        $page = $request->get('page', 1);
        $perPage = min(max((int)$request->get('per_page', 15), 5), 100);

        $filters = array_filter([
            'name' => $name,
            'has_image' => $hasImage ? ($hasImage === '1' || $hasImage === 'true') : null,
            'rooms_count' => is_array($roomsCount) ? $roomsCount : [],
            'area_min' => $areaMin ? (float)$areaMin : null,
            'area_max' => $areaMax ? (float)$areaMax : null,
        ], fn($value) => $value !== null && $value !== []);

        try {
            $results = $this->tireServiceRepository->searchWithFilters($filters, $perPage);
            $stats = $this->tireServiceRepository->getFilterStats();

            return response()->json([
                'success' => true,
                'data' => $results->items(),
                'pagination' => [
                    'current_page' => $results->currentPage(),
                    'last_page' => $results->lastPage(),
                    'per_page' => $results->perPage(),
                    'total' => $results->total(),
                    'from' => $results->firstItem(),
                    'to' => $results->lastItem(),
                ],
                'total_count' => $stats['total_count'], // Общее количество без фильтров
                'filters_applied' => $filters,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
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
        $tireService = $this->tireServiceRepository->findById($id);

        if (!$tireService) {
            return response()->json([
                'success' => false,
                'message' => 'Шиномонтажный сервис не найден',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tireService,
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
