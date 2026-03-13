<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');
Route::redirect('/admin', '/admin/login')->name('adminLogin');

Route::prefix('admin')->middleware(['guest.jwt', 'throttle.admin:5,1'])->group(function () {
    Route::get('/login',    [LoginController::class, 'loadLoginView'])->name('adminLoginViewPage');
    Route::post('/login',   [LoginController::class, 'adminLogin'])->name('adminLoginPost');
    Route::get('/register', [LoginController::class, 'loadRegisterView'])->name('adminRegister');
});

Route::prefix('admin')->middleware(['auth.jwt', 'throttle.admin:60,1'])->group(function () {
    // Auth
    Route::post('/logout', [LoginController::class, 'logout'])->name('adminLogout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'loadDashboardView'])->name('adminDashboard');

    // Activity
    Route::get('/your-activity', [ActivityController::class, 'loadActivityView'])->name('adminActivity');

    // System Activity
    Route::prefix('system-activity')->name('adminSystemActivity.')->group(function () {
        Route::get('/',             [SystemController::class, 'index'])->name('index');
        Route::get('/fetch',        [SystemController::class, 'fetchSystemActivities'])->name('fetch');
        Route::get('/{activity}',   [SystemController::class, 'loadSystemActivityDetailView'])->name('show');
        Route::delete('/bulk',      [SystemController::class, 'deleteSystemActivities'])->name('bulkDelete');
    });

    // Auth Users
    Route::prefix('auth-user')->name('adminAuthUser.')->group(function () {
        Route::get('/',              [AuthController::class, 'index'])->name('index');
        Route::get('/list',          [AuthController::class, 'fetchAllAuthUsers'])->name('list');
        Route::post('/',             [AuthController::class, 'createAndUpdateAuthUser'])->name('store');
        Route::delete('/bulk',       [AuthController::class, 'deleteMultipleAuthUser'])->name('bulkDelete');
        Route::get('/{user}',        [AuthController::class, 'fetchAuthUserDetails'])->name('show');
    });

    // Auth Permissions
    Route::prefix('/auth-permission')->name('adminAuthPermission.')->group(function () {
        Route::get('/',             [AuthController::class, 'loadAuthPermissionView'])->name('index');
        Route::get('/list',         [AuthController::class, 'fetchAllAuthPermission'])->name('list');
        Route::post('/',            [AuthController::class, 'storeAuthPermission'])->name('store');
    });

    // Users
    Route::get('/user', [UserController::class, 'loadUserView'])->name('adminUser');

    // Products
    Route::prefix('category')->name('adminProduct.')->group(function () {
        Route::get('/',         [ProductController::class, 'loadCategoryView'])->name('category');
        Route::get('/sub',      [ProductController::class, 'loadSubCategoryView'])->name('subCategory');
    });
});

// 404 Fallback
Route::fallback(fn() => view('templates.admin.errors.page-not-found'));
