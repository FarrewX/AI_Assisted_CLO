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
use App\Http\Controllers\CourseManagementController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

Route::middleware('web')->group(function () {

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
        // Route::get('/generate/show', [GenerateController::class, 'showGenerated'])->name('show.generated');
        Route::post('/generate/save', [GenerateController::class, 'saveGeneratedText'])->name('save.generate');

        Route::get('/message', function () {
            return view('message');
        })->name('message');

        Route::get('/TQF', function () {
            return view('TQF');
        })->name('TQF');

        Route::get('/editdoc', [DocumentController::class, 'editdoc'])->name('editdoc');

        Route::get('/export-docx', [DocumentController::class, 'exportdocx'])->name('export.docx');

        Route::post('/updatestartprompt', [CourseController::class, 'updateStartPrompt']);
        Route::post('/getprompt', [CourseController::class, 'getPrompt']);
        Route::post('/saveprompt', [CourseController::class, 'savePrompt'])->name('save.prompt');

        Route::patch('/savedataedit', [DocumentController::class, 'savedataedit']);
        Route::get('/getdataedit', [DocumentController::class, 'getInitialData']);
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/management', function () {
            return view('management');
        })->name('management');

        Route::get('/notification', [NotificationController::class, 'index'])->name('notification');

        Route::get('/send-email', [EmailController::class, 'showForm'])->name('email.form');
        Route::post('/send-email', [EmailController::class, 'send'])->name('send.email');
        Route::get('/management/email', [EmailController::class, 'index'])->name('management.gmail');
        Route::post('/management/email', [EmailController::class, 'save'])->name('email.save');

        Route::get('/management/plos', [PlosController::class, 'index'])->name('plos.index');
        Route::post('/management/plos/update/{id}', [PlosController::class, 'update'])->name('plos.update');
        Route::post('/management/plos/create', [PlosController::class, 'create'])->name('plos.create');
        Route::delete('/management/plos/delete/{id}', [PlosController::class, 'destroy'])->name('plos.destroy');

        Route::get('/management/courses', [CourseyearsController::class, 'index'])->name('courses');
        Route::post('/management/courses/{courseId}/professor', [CourseyearsController::class, 'store'])->name('professor.store');
        Route::put('/management/courses/{courseId}/professor/{id}', [CourseyearsController::class, 'update'])->name('professor.update');
        Route::delete('/management/courses/{courseId}/professor/{id}', [CourseyearsController::class, 'destroy'])->name('professor.destroy');

        Route::get('/management/curriculum', [CurriculumController::class, 'index'])->name('curriculum.index');
        Route::post('/management/curriculum', [CurriculumController::class, 'store'])->name('curriculum.store');
        Route::put('/management/curriculum/{id}', [CurriculumController::class, 'update'])->name('curriculum.update');
        Route::delete('/management/curriculum/{id}', [CurriculumController::class, 'destroy'])->name('curriculum.destroy');

        Route::get('/management/addcourses', [CourseManagementController::class, 'index'])->name('addcourse.list');
        Route::post('/management/addcourses', [CourseManagementController::class, 'store'])->name('addcourse.create');
        Route::put('/management/addcourses/{id}', [CourseManagementController::class, 'update'])->name('addcourse.update');
        Route::delete('/management/addcourses/{id}', [CourseManagementController::class, 'destroy'])->name('addcourse.destroy');
        Route::post('/management/addcourses/import', [CourseManagementController::class, 'import'])->name('addcourse.import');

        Route::get('/management/users', [UserController::class, 'index'])->name('management.users');
        Route::post('/management/users', [UserController::class, 'store'])->name('management.users.store');
        Route::put('/management/users/{id}/update-role', [UserController::class, 'updateRole'])->name('management.users.updateRole');
        Route::delete('/management/users/{id}', [UserController::class, 'destroy'])->name('management.users.destroy');
        Route::post('/management/users/{id}/restore', [UserController::class, 'restore'])->name('management.users.restore');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    });

});