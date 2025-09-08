<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// หน้า login/register ไม่ต้องล็อกอิน
Route::get('/login', [AuthController::class, "login"])->name('login');
Route::post('/login', [AuthController::class, "loginPost"])->name('login.post');
Route::get('/register', [AuthController::class, "register"])->name('register');
Route::post('/register', [AuthController::class, "registerPost"])->name('register.post');

// หน้าที่ต้องล็อกอิน
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('home');
    });

    Route::get('/form', function () {
        return view('form');
    })->name('form');

    Route::get('/about', function () {
        return view('about');
    })->name('about');

    Route::get('/guide', function () {
        return view('guide');
    })->name('guide');

    Route::get('/notification', function () {
        return view('notification');
    })->name('notification');

    Route::get('/message', function () {
        return view('message');
    })->name('message');

    Route::get('/clo', function () {
        return view('clo');
    })->name('clo');
});
