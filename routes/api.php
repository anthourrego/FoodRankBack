<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventProducts\EventProductsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/events', [EventController::class, 'index']);
Route::get('/events-products', [EventProductsController::class, 'index']);
