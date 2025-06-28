<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/dashboard/metricas', [DashboardController::class, 'getMetricas']);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);