<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\TireServiceController::class, 'index'])->name('tire-services.index');

Route::get('/tire-service/{id}', [\App\Http\Controllers\TireServiceController::class, 'show'])->name('tire-services.show');
