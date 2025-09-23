<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OllamaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlosController;
use App\Http\Controllers\CourseyearsController;
use Illuminate\Support\Facades\Auth;

// หน้า login/register ไม่ต้องล็อกอิน
Route::get('/login', [AuthController::class, "login"])->name('login');
Route::post('/login', [AuthController::class, "loginPost"])->name('login.post');
Route::get('/register', [AuthController::class, "register"])->name('register');
Route::post('/register', [AuthController::class, "registerPost"])->name('register.post');

// หน้าที่ต้องล็อกอิน
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [CourseController::class, 'index'])->name('home');
    
    Route::get('/form', function () {
        $user = Auth::user();

        $courses = app(\App\Http\Controllers\CourseController::class)->formdata($user);
        $plos = app(\App\Http\Controllers\PlosController::class)->plos();


        return view('form', compact('courses', 'plos'));
    });

    Route::post('/generate', [OllamaController::class, 'generateText']);

    Route::get('/about', function () {
        return view('about');
    })->name('about');

    Route::get('/guide', function () {
        return view('guide');
    })->name('guide');

    Route::get('/setting', function () {
        return view('setting');
    })->name('setting')->middleware('admin');

    Route::get('/plos', [PlosController::class, 'index'])->name('plos.index')->middleware('admin');

    Route::get('/notification', [NotificationController::class, 'index'])
        ->name('notification')
        ->middleware('admin');

    Route::get('/message', function () {
        return view('message');
    })->name('message');

    Route::get('/clo', function () {
        return view('clo');
    })->name('clo');

    Route::get('/courses', [CourseyearsController::class, 'index'])->name('courses')->middleware('admin');
});

Route::post('/plos/update/{id}', [PlosController::class, 'update'])->name('plos.update')->middleware('admin');
Route::post('/plos/create', [PlosController::class, 'create'])->name('plos.create')->middleware('admin');
Route::delete('/plos/delete/{id}', [PlosController::class, 'destroy'])->name('plos.destroy')->middleware('admin');

Route::post('/courses/{courseId}/teachers', [CourseyearsController::class, 'store'])->name('teachers.store')->middleware('admin');
Route::put('/courses/{courseId}/teachers/{id}', [CourseyearsController::class, 'update'])->name('teachers.update')->middleware('admin');
Route::delete('/courses/{courseId}/teachers/{id}', [CourseyearsController::class, 'destroy'])->name('teachers.destroy')->middleware('admin');

Route::post('/updatestartprompt', [CourseController::class, 'updateStartPrompt']);
Route::post('/saveprompt', [CourseController::class, 'savePrompt'])->name('save.prompt');