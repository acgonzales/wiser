<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

Route::view('/login', 'auth.login')->name('login');
Route::view('/signup', 'auth.signup')->name('signup');

Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/signup', [AuthController::class, 'signup'])->name('auth.signup');

Route::post('/voucher', [VoucherController::class, 'store'])->name('voucher.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::post('/voucher/claim', [VoucherController::class, 'claim'])->name('voucher.claim');
});