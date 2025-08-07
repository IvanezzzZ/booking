<?php

declare(strict_types=1);

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.api.token')
    ->controller(BookingController::class)
    ->prefix('bookings')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::post('/{booking}/slots', 'addSlot')->middleware('can:addSlot,booking');
        Route::patch('/{booking}/slots/{slot}', 'updateSlot')->middleware('can:updateSlot,booking');
        Route::delete('/{booking}', 'destroy')->middleware('can:destroy,booking');
});
