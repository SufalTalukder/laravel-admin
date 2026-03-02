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

// Route::get('/admin/system-activity/fetch', [SystemController::class, 'fetchSystemActivities'])->name('adminSystemActivityStatusView');

// After Login Routes Group
Route::prefix('admin')->middleware('auth.jwt')->group(function () {
    Route::get('/logout', [LoginController::class, 'logout']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('adminLogout');

    Route::get('/dashboard', [DashboardController::class, 'loadDashboardView'])->name('adminDashboard');

    Route::get('/your-activity', [ActivityController::class, 'loadActivityView'])->name('adminActivity');

    Route::get('/system-activity', [SystemController::class, 'index'])->name('adminSystemActivityView');
    Route::get('/system-activity/fetch', [SystemController::class, 'fetchSystemActivities'])->name('adminSystemActivityStatusView');
    Route::get('/system-activity/{id}', [SystemController::class, 'loadSystemActivityDetailView'])->name('adminSystemActivityDetailView');
    Route::post('/admin/system-activity/delete', [SystemController::class, 'deleteSystemActivities'])->name('adminSystemActivityDeleteView');

    Route::get('/auth-user', [AuthController::class, 'loadAuthView'])->name('adminAuthUser');
    Route::get('/auth-permission', [AuthController::class, 'loadAuthPermissionView'])->name('adminAuthPermission');
    Route::get('/user', [UserController::class, 'loadUserView'])->name('adminUser');
    Route::get('category', [ProductController::class, 'loadCategoryView'])->name('adminProductCategory');
    Route::get('/sub-category', [ProductController::class, 'loadSubCategoryView'])->name('adminProductSubCategory');
});

// 404 Not Found Route
Route::fallback(function () {
    return view('templates.admin.errors.page-not-found');
});
