<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TireServiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Test endpoint
Route::get('/test', function () {
    return response()->json(['status' => 'ok', 'message' => 'API works']);
});

// API маршруты для шиномонтажных сервисов
Route::prefix('tire-services')->group(function () {
    Route::get('/', [TireServiceController::class, 'index'])->name('api.tire-services.index');
    Route::get('/stats', [TireServiceController::class, 'stats'])->name('api.tire-services.stats');
    Route::get('/{id}', [TireServiceController::class, 'show'])->name('api.tire-services.show');
    Route::post('/', [TireServiceController::class, 'store'])->name('api.tire-services.store');
    Route::put('/{id}', [TireServiceController::class, 'update'])->name('api.tire-services.update');
    Route::delete('/{id}', [TireServiceController::class, 'destroy'])->name('api.tire-services.destroy');
}); 