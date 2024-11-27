<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;

Route::middleware('admin')->prefix('admin')->group(function () {
    Route::controller(AdminAuthController::class)->group(function () {
        Route::view('dashboard', 'admin.dashboard')->name('admin.dashboard');
        Route::get('logout', 'logout')->name('admin.logout'); 
    });
});

Route::prefix('admin')->group(function () {
    Route::controller(AdminAuthController::class)->group(function () {
        Route::view('login', 'admin.auths.login')->name('admin.login');
        Route::post('login', 'login')->name('admin.login_submit');
        Route::view('forget-password', 'admin.auths.forget-password')->name('admin.forget_password');
        Route::post('forget-password-submit', 'forgetPasswordSubmit')->name('admin.forget_password_submit');
        Route::get('reset-password/{token}', 'resetPassword')->name('admin.reset_password');
        Route::post('reset-password', 'resetPasswordSubmit')->name('admin.reset_password_submit');
    });
});