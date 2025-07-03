<?php

/**
 * @file: TireServiceSeeder.php
 * @description: Seeder для генерации тестовых данных шиномонтажных сервисов
 * @dependencies: TireServiceFactory
 * @created: 2024-01-01
 */

namespace Database\Seeders;

use App\Models\Domain\TireService\Models\TireService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TireServiceSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Запуск генерации тестовых данных
     */
    public function run(): void
    {
        $this->command->info('Начинаем генерацию тестовых данных для шиномонтажных сервисов...');

        // Генерируем 10 000 записей порциями для оптимизации памяти
        $totalRecords = 10000;
        $batchSize = 1000;
        $batches = ceil($totalRecords / $batchSize);

        for ($i = 0; $i < $batches; $i++) {
            $currentBatchSize = min($batchSize, $totalRecords - ($i * $batchSize));

            $this->command->info("Генерация батча " . ($i + 1) . " из {$batches} ({$currentBatchSize} записей)...");

            // 70% записей с изображениями
            $withImageCount = round($currentBatchSize * 0.7);
            TireService::factory($withImageCount)->withImage()->create();

            // 30% записей без изображений
            $withoutImageCount = $currentBatchSize - $withImageCount;
            TireService::factory($withoutImageCount)->withoutImage()->create();
        }

        $this->command->info("✅ Успешно сгенерировано {$totalRecords} записей шиномонтажных сервисов!");
    }
}
