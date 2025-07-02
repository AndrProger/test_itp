<?php

/**
 * @file: TireServiceFactory.php
 * @description: Factory для генерации тестовых данных шиномонтажных сервисов
 * @dependencies: TireService Model, Faker
 * @created: 2024-01-01
 */

namespace Database\Factories;

use App\Models\Domain\TireService\Models\TireService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\\Models\\Domain\\TireService\\Models\\TireService>
 */
class TireServiceFactory extends Factory
{
    protected $model = TireService::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceTypes = [
            'Шиномонтаж', 'СТО', 'Автосервис', 'Мойка', 'Автозапчасти',
            'Диагностика', 'Ремонт шин', 'Балансировка колес', 'Замена масла'
        ];

        $districts = [
            'Центральный', 'Северный', 'Южный', 'Западный', 'Восточный',
            'Ленинский', 'Октябрьский', 'Советский', 'Промышленный'
        ];

        $serviceType = $this->faker->randomElement($serviceTypes);
        $district = $this->faker->randomElement($districts);
        $number = $this->faker->numberBetween(1, 999);

        return [
            'name' => "$serviceType \"$district-$number\"",
            'image' => $this->faker->boolean(70) ? 'tire-services/' . $this->faker->uuid() . '.jpg' : null,
            'rooms_count' => $this->faker->numberBetween(1, 8),
            'floor' => $this->faker->numberBetween(1, 5),
            'area' => $this->faker->randomFloat(2, 15.0, 300.0),
            'description' => $this->generateDescription($serviceType),
        ];
    }

    /**
     * Состояние для записей с изображениями
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => 'tire-services/' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Состояние для записей без изображений
     */
    public function withoutImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => null,
        ]);
    }

    /**
     * Состояние для больших помещений
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'rooms_count' => $this->faker->numberBetween(5, 8),
            'area' => $this->faker->randomFloat(2, 150.0, 300.0),
        ]);
    }

    /**
     * Состояние для малых помещений
     */
    public function small(): static
    {
        return $this->state(fn (array $attributes) => [
            'rooms_count' => $this->faker->numberBetween(1, 3),
            'area' => $this->faker->randomFloat(2, 15.0, 80.0),
        ]);
    }

    /**
     * Генерация описания услуг
     */
    private function generateDescription(string $serviceType): string
    {
        $services = [
            'Шиномонтаж' => [
                'Замена и ремонт шин', 'Балансировка колес', 
                'Шиномонтажные работы', 'Хранение шин'
            ],
            'СТО' => [
                'Техническое обслуживание', 'Диагностика автомобиля',
                'Ремонт двигателя', 'Замена масла'
            ],
            'Автосервис' => [
                'Комплексное обслуживание', 'Ремонт подвески',
                'Электрика автомобиля', 'Кузовной ремонт'
            ],
            'Мойка' => [
                'Мойка автомобилей', 'Химчистка салона',
                'Полировка кузова', 'Антикоррозийная обработка'
            ],
        ];

        $serviceList = $services[$serviceType] ?? ['Качественные автоуслуги', 'Профессиональное обслуживание'];
        $maxCount = min(count($serviceList), 4);
        $selectedServices = $this->faker->randomElements($serviceList, $this->faker->numberBetween(2, $maxCount));

        $description = "Предлагаем следующие услуги: " . implode(', ', $selectedServices) . ". ";
        $description .= "Опытные мастера, качественное оборудование, гарантия на все виды работ. ";
        $description .= "Работаем ежедневно с " . $this->faker->numberBetween(8, 10) . ":00 до " . 
                       $this->faker->numberBetween(18, 22) . ":00.";

        return $description;
    }
}
