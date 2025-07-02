<?php

/**
 * @file: TireService.php
 * @description: Модель для сущности "Шиномонтаж"
 * @dependencies: Eloquent Model
 * @created: 2024-01-01
 */

namespace App\Models\Domain\TireService\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class TireService extends Model
{
    use HasFactory;

    protected $table = 'tire_services';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\TireServiceFactory::new();
    }

    protected $fillable = [
        'name',
        'image',
        'rooms_count',
        'floor',
        'area',
        'description',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'rooms_count' => 'integer',
        'floor' => 'integer',
    ];

    /**
     * Атрибуты, которые должны добавляться к JSON
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * Scope для поиска по названию с правильной сортировкой
     */
    public function scopeSearchByName(Builder $query, ?string $name): Builder
    {
        if (empty($name)) {
            return $query;
        }

        return $query->where(function($q) use ($name) {
            $q->where('name', 'LIKE', $name)  // Точное совпадение
                ->orWhere('name', 'LIKE', $name . '%')  // Начинается с запроса
                ->orWhere('name', 'LIKE', '%' . $name . '%');  // Содержит запрос
        })->orderByRaw("
            CASE 
                WHEN name LIKE ? THEN 1
                WHEN name LIKE ? THEN 2
                ELSE 3
            END
        ", [$name, $name . '%']);
    }

    /**
     * Scope для фильтрации по наличию изображения
     */
    public function scopeWithImage(Builder $query, ?bool $hasImage = null): Builder
    {
        if ($hasImage === null) {
            return $query;
        }

        return $hasImage 
            ? $query->whereNotNull('image')
            : $query->whereNull('image');
    }

    /**
     * Scope для фильтрации по количеству комнат
     */
    public function scopeByRoomsCount(Builder $query, ?array $roomsCounts = null): Builder
    {
        if (empty($roomsCounts)) {
            return $query;
        }

        return $query->whereIn('rooms_count', $roomsCounts);
    }

    /**
     * Scope для фильтрации по площади
     */
    public function scopeByAreaRange(Builder $query, ?float $minArea = null, ?float $maxArea = null): Builder
    {
        if ($minArea !== null) {
            $query->where('area', '>=', $minArea);
        }

        if ($maxArea !== null) {
            $query->where('area', '<=', $maxArea);
        }

        return $query;
    }

    /**
     * Accessors
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        // Кодируем имя файла для корректной работы с пробелами
        $encodedPath = 'tire-services/' . rawurlencode($this->image);
        
        return asset('storage/' . $encodedPath);
    }

    /**
     * Проверка наличия изображения
     */
    public function hasImage(): bool
    {
        return !empty($this->image);
    }
}
