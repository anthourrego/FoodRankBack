<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventProducts\EventProductsController;
use App\Http\Controllers\Api\Reviews\ReviewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::get('/events', [EventController::class, 'index']);
Route::get('/events-products/find', [EventProductsController::class, 'findEventProduct']);
Route::get('/events-products', [EventProductsController::class, 'index']);
Route::post('/reviews/save-vote',[ReviewsController::class, 'store']);
Route::get('/reviews/ranking/event/{idEvent}',[ReviewsController::class, 'getRanking']);
Route::get('/reviews/ranking/event-product/{event_product_id}',[ReviewsController::class, 'getDetailRankingProduct']);
Route::get('/imageproduct/{product}', [EventProductsController::class, 'showImage']);
Route::get('/imageproduct', [EventProductsController::class, 'showImage']);


Route::prefix('configurations')->group(base_path('src/admin/Configuration/Infrastructure/Routes/api.php'));

