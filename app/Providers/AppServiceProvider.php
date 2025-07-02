<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\TireService\Repositories\TireServiceRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем репозиторий для dependency injection
        $this->app->singleton(TireServiceRepository::class, function ($app) {
            return new TireServiceRepository();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
