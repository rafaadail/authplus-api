<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::post('/auth/refresh', [AuthController::class, 'refresh'])->middleware('throttle:refresh');

Route::middleware(['auth:api', 'throttle:api'])->group(function () {

    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
