<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;

use App\Http\Controllers\PanitiaController;

Route::get('/', [EventController::class, 'index']);

Route::get('/login', function () {
    return view('layout.login');
});

Route::get('/event/{id}', [EventController::class, 'show'])->name('event.detail');

Route::get('/event/{id}/daftar', [EventController::class, 'formDaftar'])->name('event.daftar');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/tim-keuangan', function () {
    return view('admin.timKeuangan');
})->name('admin.timKeuangan');

Route::get('/admin/tambah-tim-keuangan', function () {
    return view('admin.tambahTimKeuangan');
})->name('admin.tambahTimKeuangan');

use App\Http\Controllers\TimKeuanganController;
Route::get('/admin/edit-tim-keuangan/{id}', [TimKeuanganController::class, 'edit'])->name('admin.editTimKeuangan');


// Panitia routes
Route::get('/admin/panitia', function () {
    return view('admin.panitia');
})->name('admin.panitia');

Route::get('/admin/tambah-tim-panitia', function () {
    return view('admin.tambahPanitia');
})->name('admin.tambahTimPanitia');

Route::get('/admin/edit-tim-panitia/{id}', [PanitiaController::class, 'edit'])->name('admin.editTimPanitia');

// Panit Event (for panitia)
use App\Http\Controllers\PanitEventController;
Route::get('/panit/event', [PanitEventController::class, 'index'])->name('panit.event');

// Route for edit event form (panitia)
Route::get('/panit/edit-event/{id}', [PanitEventController::class, 'edit'])->name('panit.editEvent');

Route::get('/panit/scan', function () {
    return view('panit.scan');
})->name('panit.scan');

// Profile page route (admin)
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
Route::get('/admin/profile', function () {
    // Ambil id user dari session (misal 'user_id'), sesuaikan dengan implementasi login kamu
    $userId = session('user_id');
    $user = [
        'name' => '',
        'email' => '',
        'status' => '',
    ];
    if ($userId) {
        // Ambil data user dari Node.js
        $response = Http::get('http://localhost:3000/api/users/profile/' . $userId);
        if ($response->successful()) {
            $user = $response->json();
        }
    }
    return view('admin.profile', compact('user'));
})->name('admin.profile');

Route::get('/panit/profile', function () {
    // Ambil id user dari session (misal 'user_id'), sesuaikan dengan implementasi login kamu
    $userId = session('user_id');
    $user = [
        'name' => '',
        'email' => '',
        'status' => '',
    ];
    if ($userId) {
        // Ambil data user dari Node.js
        $response = Http::get('http://localhost:3000/api/users/profile/' . $userId);
        if ($response->successful()) {
            $user = $response->json();
        }
    }
    return view('panit.profile', compact('user'));
})->name('panit.profile');

Route::get('/panit/dashboard', function () {
    return view('panit.dashboard');
})->name('panit.dashboard');

Route::get('/panit/tambah-event', function () {
    return view('panit.tambahEvent');
})->name('panit.tambahEvent');

// Route untuk form tambah event panitia
Route::get('/panit/tambah-event', function () {
    return view('panit.tambahEvent');
})->name('panit.tambahEvent');


// Route::get('/event-saya', [EventController::class, 'eventSaya'])->name('event.saya');
Route::get('/event-saya', function () {
    return view('member.list_event'); 
})->name('event.saya');
Route::get('/event-saya/{id}', function () {
	return view('member.list_event_detail'); // file: resources/views/event-detail.blade.php
});
Route::get('/riwayat-pembayaran', function () {
    return view('member.riwayat_pembayaran'); 
})->name('riwayat.pembayaran');
Route::get('/event-saya/update-bukti/{registrasiId}', function ($registrasiId) {
    return view('member.update_bukti', compact('registrasiId'));
});



Route::get('/keuangan/dashboard', function () {
    return view('keuangan.dashboard');
})->name('keuangan.dashboard');
// Route::get('/keuangan/event/event-detail/{id}', [EventController::class, 'showEventDetail'])->name('keuangan.event.detail');
Route::get('/keuangan/event/event-detail/{id}', function () {
    return view('keuangan.detail-event');
})->name('keuangan.event.detail');

