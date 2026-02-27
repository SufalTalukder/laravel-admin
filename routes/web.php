<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::redirect('/', '/admin/login');
Route::redirect('/admin', '/admin/login')->name('adminLogin');

// Before Login Routes Group
Route::prefix('admin')->middleware('guest.jwt')->group(function () {
    Route::get('/login', [LoginController::class, 'loadLoginView'])->name('adminLoginViewPage');
    Route::post('/login', [LoginController::class, 'adminLogin']);
    Route::get('/register', [LoginController::class, 'loadRegisterView'])->name('adminRegister');
});

// After Login Routes Group
Route::prefix('admin')->middleware('auth.jwt')->group(function () {
    Route::get('/logout', [LoginController::class, 'logout']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('adminLogout');
    Route::get('/dashboard', [DashboardController::class, 'loadDashboardView'])->name('adminDashboard');
    Route::get('/your-activity', [ActivityController::class, 'loadActivityView'])->name('adminActivity');
    Route::get('/system-activity', [SystemController::class, 'loadSystemActivityView'])->name('adminSystemActivity');
    Route::get('/system-activity/{id}', [SystemController::class, 'loadSystemActivityView'])->name('adminSystemActivityView');
    Route::get('/auth-user', [AuthController::class, 'loadAuthView'])->name('adminAuthUser');
    Route::get('/auth-permission', [AuthController::class, 'loadAuthPermissionView'])->name('adminAuthPermission');
    Route::get('/user', [UserController::class, 'loadUserView'])->name('adminUser');
    Route::get('category', [ProductController::class, 'loadCategoryView'])->name('adminProductCategory');
    Route::get('/sub-category', [ProductController::class, 'loadSubCategoryView'])->name('adminProductSubCategory');
});

// 404 Not Route
Route::fallback(function () {
    return view('templates.admin.errors.page-not-found');
});
