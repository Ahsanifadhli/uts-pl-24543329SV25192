<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatatanController;
use Illuminate\Support\Facades\Route;

// Endpoint publik
Route::post('/login', [AuthController::class, 'login']);

// Endpoint yang membutuhkan token (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('catatan', CatatanController::class);
});
