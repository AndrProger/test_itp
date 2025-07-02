<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @file: create_tire_services_table.php
     * @description: Миграция для создания таблицы шиномонтажных сервисов
     * @dependencies: Базовая структура Laravel
     * @created: 2024-01-01
     */
    
    public function up(): void
    {
        Schema::create('tire_services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Название шиномонтажа');
            $table->string('image')->nullable()->comment('Путь к изображению');
            $table->integer('rooms_count')->unsigned()->comment('Количество комнат');
            $table->integer('floor')->unsigned()->comment('Этаж');
            $table->decimal('area', 8, 2)->comment('Площадь в кв.м');
            $table->text('description')->nullable()->comment('Описание');
            $table->timestamps();

            // Индексы для оптимизации поиска
            $table->index('name', 'tire_services_name_index');
            $table->index('rooms_count', 'tire_services_rooms_count_index');
            $table->index('floor', 'tire_services_floor_index');
            $table->index('area', 'tire_services_area_index');
            $table->index(['name', 'rooms_count'], 'tire_services_name_rooms_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tire_services');
    }
};
