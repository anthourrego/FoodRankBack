<?php

use Illuminate\Support\Facades\Route;
use src\admin\Configuration\Infrastructure\Controllers\ConfigurationController;

Route::get('/',[ConfigurationController::class, 'index']);
Route::post('/',[ConfigurationController::class, 'store']);
/* Route::put('/{key}',[ConfigurationController::class, 'update']); */
