<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OllamaController;
use App\Http\Controllers\NotificationController;

// หน้า login/register ไม่ต้องล็อกอิน
Route::get('/login', [AuthController::class, "login"])->name('login');
Route::post('/login', [AuthController::class, "loginPost"])->name('login.post');
Route::get('/register', [AuthController::class, "register"])->name('register');
Route::post('/register', [AuthController::class, "registerPost"])->name('register.post');

// หน้าที่ต้องล็อกอิน
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [CourseController::class, 'index'])->name('home');
    Route::get('/form', [CourseController::class, 'formdata'])->name('form');

    Route::post('/generate', [OllamaController::class, 'generateText']);

    Route::get('/about', function () {
        return view('about');
    })->name('about');

    Route::get('/guide', function () {
        return view('guide');
    })->name('guide');

    Route::get('/notification', [NotificationController::class, 'index'])
        ->name('notification')
        ->middleware('admin');

    Route::get('/message', function () {
        return view('message');
    })->name('message');

    Route::get('/clo', function () {
        return view('clo');
    })->name('clo');
});