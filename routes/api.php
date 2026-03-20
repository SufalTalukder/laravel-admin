<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* =========================================================================================================
================================================= API Routes ===============================================
========================================================================================================= */

Route::prefix('v1')->group(function () {
    // Auth Register
    Route::middleware(['api.key'])->group(function () {
        Route::post('/auth/register', [AuthController::class, 'authUserRegister']);
    });

    // Auth Login
    Route::middleware(['api.key'])->group(function () {
        Route::post('/auth/login', [AuthController::class, 'authUserLogin']);
    });

    // Fetch Auth Details
    Route::middleware(['api.key', 'jwt.cookie'])->group(function () {
        Route::get('/auth/details', [AuthController::class, 'fetchAuthUserDetails']);
    });

    // Create / Update Auth User
    Route::middleware(['api.key', 'jwt.cookie'])->group(function () {
        Route::post('/auth/save', [AuthController::class, 'createOrUpdateAuthUser']);
    });

    // Get All Auth List
    Route::middleware(['api.key', 'jwt.cookie'])->group(function () {
        Route::get('/auth/list', [AuthController::class, 'fetchAuthUsersList']);
    });

    // Delete Auth User
    Route::middleware(['api.key', 'jwt.cookie'])->group(function () {
        Route::delete('/auth/delete', [AuthController::class, 'deleteAuthUser']);
    });

    // Upload Auth User
    Route::middleware(['api.key', 'jwt.cookie'])->group(function () {
        Route::post('/auth/upload-image', [AuthController::class, 'uploadImage']);
    });
});
