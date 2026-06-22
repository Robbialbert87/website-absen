<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PegawaiController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('login', [AuthController::class, 'login']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'me']);
});

// Public pegawai endpoint
Route::get('pegawai', [PegawaiController::class, 'index']);

