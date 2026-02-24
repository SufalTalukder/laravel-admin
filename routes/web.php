<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::redirect('/', '/admin/login');
Route::redirect('/admin', '/admin/login')->name('adminLogin');

Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'loadLoginView'])->name('adminLogin');
    Route::get('/register', [LoginController::class, 'loadRegisterView'])->name('adminRegister');
    Route::get('/dashboard', [DashboardController::class, 'loadDashboardView'])->name('adminDashboard');
    Route::get('/your-activity', [ActivityController::class, 'loadActivityView'])->name('adminActivity');
    Route::get('/system-activity', [SystemController::class, 'loadSystemActivityView'])->name('adminSystemActivity');
    Route::get('/auth-user', [AuthController::class, 'loadAuthView'])->name('adminAuthUser');
    Route::get('/auth-permission', [AuthController::class, 'loadAuthPermissionView'])->name('adminAuthPermission');
    Route::get('/user', [UserController::class, 'loadUserView'])->name('adminUser');
});
