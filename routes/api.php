<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Rentals\ReturnRentalController;
use App\Http\Controllers\Api\V1\Rentals\StartRentalController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/v1/rentals/start', StartRentalController::class);
    Route::post('/v1/rentals/{rental}/return', ReturnRentalController::class);
});
