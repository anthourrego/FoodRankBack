<?php

use Illuminate\Support\Facades\Route;
use src\admin\Events\Infrastructure\Controllers\EventsController;

Route::get('/active', [EventsController::class, 'getEventsActive'])->name('events.active');
Route::get('/products-event/{id}', [EventsController::class, 'getProductsEvent'])->name('events.products');