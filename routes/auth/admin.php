<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;

Route::middleware('admin')->prefix('admin')->group(function () {
    
    Route::view('dashboard', 'admin.dashboard')->name('admin.dashboard');

    Route::controller(AdminAuthController::class)->group(function () {
        Route::get('logout', 'logout')->name('admin.logout'); 
    });
});

Route::prefix('admin')->group(function () {

    Route::view('forget-password', 'admin.auths.forget-password')->name('admin.forget_password');
    Route::view('login', 'admin.auths.login')->name('admin.login');

    Route::controller(AdminAuthController::class)->group(function () {
        Route::post('login', 'login')->name('admin.login_submit');
        Route::post('forget-password-submit', 'forgetPasswordSubmit')->name('admin.forget_password_submit');
        Route::get('reset-password/{token}', 'resetPassword')->name('admin.reset_password');
        Route::post('reset-password', 'resetPasswordSubmit')->name('admin.reset_password_submit');
    });
});