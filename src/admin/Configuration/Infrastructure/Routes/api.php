<?php

use Illuminate\Support\Facades\Route;
use src\admin\Configuration\Infrastructure\Controllers\ConfigurationController;

Route::get('/event/{eventId}',[ConfigurationController::class, 'show']);
Route::get('/',[ConfigurationController::class, 'index']);
Route::post('/',[ConfigurationController::class, 'store']);
