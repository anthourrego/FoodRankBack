<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventProducts\EventProductsController;
use App\Http\Controllers\Api\Reviews\ReviewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/events', [EventController::class, 'index']);
Route::get('/events-products/find', [EventProductsController::class, 'findEventProduct']);
Route::get('/events-products', [EventProductsController::class, 'index']);
Route::post('/reviews/save-vote',[ReviewsController::class, 'store']);
Route::get('/reviews/ranking/event/{idEvent}',[ReviewsController::class, 'getRanking']);

Route::get('/imageproduct/{product}', [EventProductsController::class, 'showImage']);
Route::get('/imageproduct', [EventProductsController::class, 'showImage']);
