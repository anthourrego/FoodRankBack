<?php

use Illuminate\Support\Facades\Route;
use src\admin\Events\Infrastructure\Controllers\EventsController;


Route::get('/', [EventsController::class, 'index'])->name('events.index');
Route::get('/active', [EventsController::class, 'getEventsActive'])->name('events.active');
Route::get('/products-event/{id}', [EventsController::class, 'getProductsEvent'])->name('events.products');
Route::post('/', [EventsController::class, 'store'])->name('events.store');
//update event
Route::put('/{id}', [EventsController::class, 'update'])->name('events.update');
