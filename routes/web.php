<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


Route::get('/', function () {
    return view('welcome');
});


Route::post('/api/products', [ProductController::class, 'store']);
Route::get('/api/products', [ProductController::class, 'index']);
Route::get('/api/products/{id}', [ProductController::class, 'show']);
Route::put('/api/products/{id}', [ProductController::class, 'update']);
Route::delete('/api/products/{id}', [ProductController::class, 'destroy']);