<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\OllamaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlosController;
use App\Http\Controllers\CourseyearsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\GenerateController;

// หน้า login/register ไม่ต้องล็อกอิน
Route::get('/login', [AuthController::class, "login"])->name('login');
Route::post('/login', [AuthController::class, "loginPost"])->name('login.post');
Route::get('/register', [AuthController::class, "register"])->name('register');
Route::post('/register', [AuthController::class, "registerPost"])->name('register.post');

// หน้าที่ต้องล็อกอิน
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [CourseController::class, 'index'])->name('home');
    
    Route::get('/form', function (Request $request) {
        $user = Auth::user();

        $courses = app(\App\Http\Controllers\CourseController::class)->formdata($request);
        $plos = app(\App\Http\Controllers\PlosController::class)->plos();

        return view('form', compact('courses', 'plos'));
    });

    Route::post('/generate', [OllamaController::class, 'generateText']);
    // Route::get('/generate/show', [GenerateController::class, 'showGenerated'])->name('show.generated');
    Route::post('/generate/save', [GenerateController::class, 'saveGeneratedText'])->name('save.generate');

    Route::get('/about', function () {
        return view('about');
    })->name('about');

    Route::get('/guide', function () {
        return view('guide');
    })->name('guide');

    Route::get('/management', function () {
        return view('management');
    })->name('management')->middleware('admin');

    Route::get('/plos', [PlosController::class, 'index'])->name('plos.index')->middleware('admin');

    Route::get('/notification', [NotificationController::class, 'index'])->name('notification')->middleware('admin');

    Route::get('/message', function () {
        return view('message');
    })->name('message');

    Route::get('/TQF', function () {
        return view('TQF');
    })->name('TQF');
    
    Route::get('/courses', [CourseyearsController::class, 'index'])->name('courses')->middleware('admin');

    Route::get('/preview', [DocumentController::class, 'preview'])->name('preview');

    Route::get('/export-docx', [DocumentController::class, 'exportForm'])->name('export.docx');

    Route::post('/updatestartprompt', [CourseController::class, 'updateStartPrompt']);
    Route::post('/getprompt', [CourseController::class, 'getPrompt']);
    Route::post('/saveprompt', [CourseController::class, 'savePrompt'])->name('save.prompt');
});

Route::middleware('admin')->group(function () {
    Route::get('/send-email', [EmailController::class, 'showForm']);
    Route::post('/send-email', [EmailController::class, 'send'])->name('send.email');

    Route::post('/plos/update/{id}', [PlosController::class, 'update'])->name('plos.update');
    Route::post('/plos/create', [PlosController::class, 'create'])->name('plos.create');
    Route::delete('/plos/delete/{id}', [PlosController::class, 'destroy'])->name('plos.destroy');

    Route::post('/courses/{courseId}/professor', [CourseyearsController::class, 'store'])->name('professor.store');
    Route::put('/courses/{courseId}/professor/{id}', [CourseyearsController::class, 'update'])->name('professor.update');
    Route::delete('/courses/{courseId}/professor/{id}', [CourseyearsController::class, 'destroy'])->name('professor.destroy');

});