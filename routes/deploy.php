<?php

use App\Http\Controllers\ClearController;
use App\Http\Controllers\MigrateController;
use App\Http\Controllers\ResendBookingEmailController;
use App\Http\Controllers\SeedController;
use App\Http\Controllers\TestMailController;
use Illuminate\Support\Facades\Route;

Route::get('/migrate', MigrateController::class)->name('migrate');
Route::get('/seed', SeedController::class)->name('seed');
Route::get('/clear', ClearController::class)->name('clear');
Route::get('/test-mail', TestMailController::class)->name('test-mail');
Route::get('/resend-booking-email', ResendBookingEmailController::class)->name('resend-booking-email');
