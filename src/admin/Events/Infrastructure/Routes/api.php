<?php

use Illuminate\Support\Facades\Route;
use src\admin\Events\Infrastructure\Controllers\EventsController;


Route::get('/', [EventsController::class, 'index'])->name('events.index');
Route::get('/active', [EventsController::class, 'getEventsActive'])->name('events.active');
Route::get('/products-event/{id}', [EventsController::class, 'getProductsEvent'])->name('events.products');
Route::post('/products-event/{eventId}/{productId}/branches',[EventsController::class,'assignedBranch'])->name('events.product.assigned-branch');
Route::post('/products-event/{eventId}', [EventsController::class, 'storeProductsEvent'])->name('events.products.store');
Route::post('/', [EventsController::class, 'store'])->name('events.store');
//update event
Route::put('/{id}', [EventsController::class, 'update'])->name('events.update');
Route::delete('/products-event/{eventId}/{productId}', [EventsController::class, 'deleteProductEvent'])->name('events.products.delete');
