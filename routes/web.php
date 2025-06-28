<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/categorias', function () {
    return view('categorias');
})->name('categorias');

Route::get('/productos', function () {
    return view('productos');
})->name('productos');