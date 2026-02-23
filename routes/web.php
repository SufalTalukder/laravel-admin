<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'templates.admin.login')->name('adminLogin');

Route::prefix('admin')->group(function () {
    Route::view('/', 'templates.admin.login')->name('adminLogin');
    Route::view('/login', 'templates.admin.login')->name('adminLogin');
    Route::view('/register', 'templates.admin.register')->name('adminRegister');
    Route::view('/dashboard', 'templates.admin.dashboard')->name('adminDashboard');
});
