<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\LanguageController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\SubCategoryController;
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
        Route::post('/user/login',                  [LoginController::class, 'userLogin']);
        Route::post('/user/verify-otp',             [LoginController::class, 'verifyOtp']);
        Route::post('/user/token/refresh',          [LoginController::class, 'refreshToken']);

        Route::get('/category-list',                [CategoryController::class, 'fetchCategoriesList']);
        Route::post('/category-wise-products',      [CategoryController::class, 'fetchCategoryWiseProducts']);
        Route::get('/subcategory-list',             [SubCategoryController::class, 'fetchSubCategoriesList']);
        Route::post('/subcategory-wise-products',   [SubCategoryController::class, 'fetchSubCategoryWiseProducts']);
        Route::get('/language-list',                [LanguageController::class, 'fetchLanguagesList']);
        Route::post('/language-wise-products',      [LanguageController::class, 'fetchLanguageWiseProducts']);
        Route::post('/product-details',             [ProductController::class, 'fetchProductDetails']);
        Route::post('/fetch-featured-products',     [ProductController::class, 'fetchFeaturedProducts']);
    });

    // After Login
    Route::middleware(['api.key', 'jwt.user'])->group(function () {
        Route::post('/user/logout',                 [LoginController::class, 'logout']);
        Route::get('/user/fetch-details',           [MyAccountController::class, 'fetchUserDetails']);
        Route::post('/user/update-details',         [MyAccountController::class, 'updateUserDetails']);

        Route::post('/user/add-update-address',     [MyAccountController::class, 'addUpdateUserAddress']);
        Route::get('/user/fetch-addresses-list',    [MyAccountController::class, 'fetchAllUserAddresses']);
        Route::post('/user/fetch-address-details',  [MyAccountController::class, 'fetchUserAddressDetails']);
        Route::post('/user/delete-address',         [MyAccountController::class, 'deleteUserAddress']);

        Route::post('/add-recently-viewed-product', [ProductController::class, 'addRecentlyViewedProduct']);
        Route::post('/recently-viewed-products',    [ProductController::class, 'fetchRecentlyViewedProducts']);
        Route::post('/manage-wishlist',             [ProductController::class, 'manageWishlist']);
        Route::post('/fetch-wishlist',              [ProductController::class, 'fetchWishlistOfUser']);
        Route::post('/fetch-all-wishlists',         [ProductController::class, 'fetchAllWishlistsOfUser']);
        Route::post('/user/manage-cart',            [ProductController::class, 'manageUserCart']);
        Route::post('/user/fetch-cart',             [ProductController::class, 'fetchUserCart']);
        Route::post('/user/fetch-all-carts',        [ProductController::class, 'fetchAllUserCarts']);
    });
});

Route::fallback(function () {
    return response()->json([
        'status' => 'Error',
        'message' => 'Invalid API route.'
    ], 404);
});
