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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Domain\TireService\Models\TireService>
 */
class TireServiceFactory extends Factory
{
    protected $model = TireService::class;

    /**
     * Доступные фотографии шиномонтажных сервисов
     */
    protected array $availableImages = [
        'orig.jpeg',
        'orig (1).jpeg',
        'XXL_height.jpeg',
        'XXL_height (1).jpeg',
        'XXL_height (2).jpeg',
        'XXL_height (3).jpeg',
        'XXL_height (4).jpeg',
        'XXL_height (5).jpeg',
        'XXL_height (6).jpeg',
        'XXL_height (7).jpeg',
        'XXL_height (8).jpeg',
        'XXL_height (9).jpeg',
        'XXXL.jpeg',
        'XXXL (1).jpeg',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $serviceNames = [
            'Автосервис Премиум',
            'Шиномонтаж Профи',
            'Быстрый Шиномонтаж',
            'Колесо Сервис',
            'Авто Мастер',
            'Шинный Центр',
            'Мобильный Шиномонтаж',
            'Экспресс Шины',
            'Автосервис 24/7',
            'Шиномонтаж Эксперт',
            'Колесо Плюс',
            'Авто Помощь',
            'Шинный Доктор',
            'Быстрые Колеса',
            'Автосервис Люкс',
            'Шиномонтаж Мастер',
            'Колесная База',
            'Авто Резина',
            'Шинный Экспресс',
            'Мастерская Шин'
        ];

        $descriptions = [
            'Профессиональный шиномонтаж с использованием современного оборудования',
            'Качественный ремонт шин и балансировка колес',
            'Быстрый и недорогой шиномонтаж в удобном месте',
            'Полный комплекс услуг по обслуживанию автомобильных шин',
            'Экспертный шиномонтаж и диагностика ходовой части',
            'Современный сервисный центр с гарантией качества',
            'Мобильный шиномонтаж с выездом к клиенту',
            'Экспресс-услуги по замене и ремонту шин',
            'Круглосуточный автосервис и шиномонтаж',
            'Профессиональное обслуживание всех типов шин'
        ];

        // Генерируем случайную дату в диапазоне от 6 месяцев назад до текущей даты
        $createdAt = $this->faker->dateTimeBetween('-6 months', 'now');
        // updated_at должен быть не раньше created_at и не позже текущей даты
        $updatedAt = $this->faker->dateTimeBetween($createdAt, 'now');

        return [
            'name' => $this->faker->randomElement($serviceNames) . ' ' . $this->faker->numberBetween(1, 99),
            'image' => $this->faker->boolean(70) ? $this->faker->randomElement($this->availableImages) : null,
            'rooms_count' => $this->faker->numberBetween(1, 8),
            'floor' => $this->faker->numberBetween(1, 15),
            'area' => $this->faker->numberBetween(20, 500),
            'description' => $this->faker->randomElement($descriptions),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }

    /**
     * Указать, что данная запись должна иметь изображение.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => $this->faker->randomElement($this->availableImages),
        ]);
    }

    /**
     * Указать, что данная запись не должна иметь изображение.
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

        $openHour = $this->faker->numberBetween(8, 10);
        $closeHour = $this->faker->numberBetween(18, 22);
        $description = "Предлагаем следующие услуги: " . implode(', ', $selectedServices) . ". ";
        $description .= "Опытные мастера, качественное оборудование, гарантия на все виды работ. ";
        $description .= "Работаем ежедневно с {$openHour}:00 до {$closeHour}:00.";

        return $description;
    }
}
