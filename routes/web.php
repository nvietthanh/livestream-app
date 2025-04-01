<?php

use App\Http\Controllers\CallController;
use App\Http\Controllers\LiveController;
use App\Http\Controllers\LiveStreamController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', [CallController::class, 'listOnline'])->name('index');
    Route::post('handshake', [CallController::class, 'handshake'])->name('handshake');

    Route::get('/streaming', [LiveStreamController::class, 'liveStream'])->name('room.livestream');
    Route::get('/streaming/{id}', [LiveStreamController::class, 'streaming'])->name('room.streaming');
    Route::post('/streaming-offer', [LiveStreamController::class, 'streamOffer'])->name('room.stream-offer');
    Route::post('/streaming-answer', [LiveStreamController::class, 'streamAnswer'])->name('room.stream-answer');
    Route::post('/send-message-streaming', [LiveStreamController::class, 'sendMessage'])->name('room.send-message');


    // Route::get('/listream', [LiveController::class, 'liveStream'])->name('listream');
    // Route::get('/streaming/{id}', [LiveController::class, 'streaming'])->name('streaming');
    // Route::post('/send-message-streaming/{id}', [LiveController::class, 'sendMessage'])->name('send-message');
    // Route::post('/stream-offer', [LiveController::class, 'makeStreamOffer']);
    // Route::post('/stream-answer', [LiveController::class, 'makeStreamAnswer']);

    // Route::get('/dashboard', function () {
    //     return Inertia::render('Dashboard');
    // })->name('dashboard');
    // Route::get('/listream', function () {
    //     return Inertia::render('Dashboard');
    // })->name('listream');
    // Route::get('/streaming/{streamId}', function () {
    //     return Inertia::render('Dashboard');
    // });
});
