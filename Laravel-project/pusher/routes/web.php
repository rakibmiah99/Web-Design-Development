<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('trigger', [\App\Http\Controllers\PusherController::class, 'trigger'])->name('trigger');
Route::post('make-call', [\App\Http\Controllers\PusherController::class, 'makeCall'])->name('make-call');
