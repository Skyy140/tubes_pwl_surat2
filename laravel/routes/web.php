<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;

Route::get('/', [EventController::class, 'index']);

Route::get('/login', function () {
    return view('layout.login');
});

Route::get('/event/{id}', [EventController::class, 'show'])->name('event.detail');

Route::get('/event/{id}/daftar', [EventController::class, 'formDaftar'])->name('event.daftar');



