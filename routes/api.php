<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\User\LoginController;
use App\Http\Controllers\Api\User\MyAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* =========================================================================================================
============================================= API Admin Routes =============================================
========================================================================================================= */

Route::prefix('v1')->group(function () {
    // Before Login
    Route::middleware(['api.key'])->group(function () {
        Route::post('/auth/register', [AuthController::class, 'authUserRegister']);
        Route::post('/auth/login', [AuthController::class, 'authUserLogin']);
    });

    // After Login
    Route::middleware(['api.key', 'jwt.cookie'])->group(function () {
        Route::get('/auth/details', [AuthController::class, 'fetchAuthUserDetails']);
        Route::post('/auth/save', [AuthController::class, 'createOrUpdateAuthUser']);
        Route::get('/auth/list', [AuthController::class, 'fetchAuthUsersList']);
        Route::delete('/auth/delete', [AuthController::class, 'deleteAuthUser']);
        Route::post('/auth/upload-image', [AuthController::class, 'uploadImage']);
    });
});


/* =========================================================================================================
=============================================== API User Routes ============================================
========================================================================================================= */

Route::prefix('v1')->group(function () {
    // Before Login
    Route::middleware(['api.key'])->group(function () {
        Route::post('/user/login',         [LoginController::class, 'userLogin']);
        Route::post('/user/verify-otp',    [LoginController::class, 'verifyOtp']);
        Route::post('/user/token/refresh', [LoginController::class, 'refreshToken']);
    });

    // After Login
    Route::middleware(['api.key', 'jwt.user'])->group(function () {
        Route::post('/user/logout',         [LoginController::class, 'logout']);
        Route::post('/user/fetch-details',  [MyAccountController::class, 'fetchUserDetails']);
        Route::post('/user/update-details', [MyAccountController::class, 'updateUserDetails']);
    });
});
