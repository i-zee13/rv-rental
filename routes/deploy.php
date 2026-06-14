<?php

use App\Http\Controllers\ClearController;
use App\Http\Controllers\MigrateController;
use Illuminate\Support\Facades\Route;

Route::get('/migrate', MigrateController::class)->name('migrate');
Route::get('/clear', ClearController::class)->name('clear');
