<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;

Route::view('login', 'user.auths.login')->name('login');
Route::view('register', 'user.auths.register')->name('register');
Route::view('forget-password', 'user.auths.forget-password')->name('forget.password');

Route::controller(UserAuthController::class)->group(function () {
    Route::post('user-registration', 'registration')->name('user.registration');
    Route::get('activate-account/{token}', 'verifyEmail')->name('verify.email');
});