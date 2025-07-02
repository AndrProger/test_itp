<?php

/**
 * @file: TireServiceRepository.php
 * @description: Репозиторий для работы с данными шиномонтажных сервисов
 * @dependencies: TireService Model, Eloquent
 * @created: 2024-01-01
 */

namespace App\Domain\TireService\Repositories;

use App\Models\Domain\TireService\Models\TireService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TireServiceRepository
{
    /**
     * Поиск с фильтрацией и пагинацией
     */
    public function searchWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TireService::query();

        // Поиск по названию
        if (!empty($filters['name'])) {
            $query->searchByName($filters['name']);
        }

        // Фильтр по наличию изображения
        if (isset($filters['has_image'])) {
            $query->withImage($filters['has_image']);
        }

        // Фильтр по количеству комнат
        if (!empty($filters['rooms_count'])) {
            $query->byRoomsCount($filters['rooms_count']);
        }

        // Фильтр по площади
        if (isset($filters['area_min']) || isset($filters['area_max'])) {
            $query->byAreaRange(
                $filters['area_min'] ?? null,
                $filters['area_max'] ?? null
            );
        }

        // Базовая сортировка (если не было поиска по названию)
        if (empty($filters['name'])) {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Получить все записи
     */
    public function getAll(): Collection
    {
        return TireService::all();
    }

    /**
     * Найти по ID
     */
    public function findById(int $id): ?TireService
    {
        return TireService::find($id);
    }

    /**
     * Создать новую запись
     */
    public function create(array $data): TireService
    {
        return TireService::create($data);
    }

    /**
     * Обновить запись
     */
    public function update(int $id, array $data): ?TireService
    {
        $tireService = $this->findById($id);
        
        if ($tireService) {
            $tireService->update($data);
            return $tireService;
        }

        return null;
    }

    /**
     * Удалить запись
     */
    public function delete(int $id): bool
    {
        $tireService = $this->findById($id);
        
        if ($tireService) {
            return $tireService->delete();
        }

        return false;
    }

    /**
     * Получить статистику для фильтров
     */
    public function getFilterStats(): array
    {
        $minArea = TireService::min('area') ?? 0;
        $maxArea = TireService::max('area') ?? 1000;
        $availableRoomsCounts = TireService::select('rooms_count')
            ->distinct()
            ->orderBy('rooms_count')
            ->pluck('rooms_count')
            ->toArray();

        return [
            'area_range' => [
                'min' => $minArea,
                'max' => $maxArea,
            ],
            'rooms_counts' => $availableRoomsCounts,
            'total_count' => TireService::count(),
            'with_images_count' => TireService::whereNotNull('image')->count(),
        ];
    }
} 