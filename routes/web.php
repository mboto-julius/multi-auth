<?php

use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Route;

Route::controller(FrontController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('about', 'about')->name('about');
});


require __DIR__ . '/auth/user.php';
require __DIR__ . '/auth/admin.php';


