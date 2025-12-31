<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventProducts\EventProductsController;
use App\Http\Controllers\Api\Reviews\ReviewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Restaurant\RestaurantController;
use App\Http\Controllers\Api\RestaurantBranch\RestaurantBranchController;
use App\Http\Controllers\Api\RestaurantProducts\RestaurantProductController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::get('cities', [RestaurantController::class, 'getCities']);

    // Restaurant
    Route::apiResource('restaurants', RestaurantController::class);
    Route::patch('restaurants/{id}/toggle-status', [RestaurantController::class, 'toggleStatus']);

    Route::prefix('restaurant-branches')->group(function () {
        Route::get('/', [RestaurantBranchController::class, 'index']);
        Route::post('/', [RestaurantBranchController::class, 'store']);
        Route::get('/{id}', [RestaurantBranchController::class, 'show']);
        Route::put('/{id}', [RestaurantBranchController::class, 'update']);
        Route::delete('/{id}', [RestaurantBranchController::class, 'destroy']);

        Route::patch('/{id}/toggle-status', [RestaurantBranchController::class, 'toggleStatus']);

        Route::get('/stats/restaurant/{restaurantId}', [RestaurantBranchController::class, 'getStatsByRestaurant']);
    });

    // Restaurant Products
    Route::prefix('products-restaurant')->group(function () {
        Route::get('/', [RestaurantProductController::class, 'index']);
        Route::get('/restaurants', [RestaurantProductController::class, 'getRestaurants']);
        Route::post('/', [RestaurantProductController::class, 'store']);
        Route::put('/{product}', [RestaurantProductController::class, 'update']);
        Route::delete('/{product}', [RestaurantProductController::class, 'destroy']);
        Route::patch('/{id}/toggle-status', [RestaurantProductController::class, 'toggleStatus']);
    });

    Route::prefix('configurations')->group(base_path('src/admin/Configuration/Infrastructure/Routes/api.php'));
});
Route::prefix('eventss')->group(base_path('src/admin/Events/Infrastructure/Routes/api.php'));

Route::get('/events', [EventController::class, 'index']);
Route::get('/events-products/find', [EventProductsController::class, 'findEventProduct']);
Route::get('/events-products', [EventProductsController::class, 'index']);
Route::post('/reviews/save-vote',[ReviewsController::class, 'store']);
Route::get('/imageproduct/{product}', [EventProductsController::class, 'showImage']);
Route::get('/imageproduct', [EventProductsController::class, 'showImage']);

Route::get('/reviews/ranking-list/event/{idEvent}/export',[ReviewsController::class, 'exportRankingList']);
Route::get('/reviews/ranking-list/event/{idEvent}',[ReviewsController::class, 'getRankingList']);
Route::get('/reviews/ranking/event/{idEvent}',[ReviewsController::class, 'getRanking']);
Route::get('/reviews/ranking/event-product/{event_product_id}',[ReviewsController::class, 'getDetailRankingProduct']);

