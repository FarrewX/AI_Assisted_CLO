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