<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/form', function () {
    return view('form');
})->name('form');

Route::get('/login', function () {
    return view('login');
})->name('login');

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