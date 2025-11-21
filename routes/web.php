<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/display', [QueueController::class, 'display'])->name('queue.display');
Route::get('/display-full', function () {
    $current = \App\Models\Queue::where('status', 'dipanggil')->first();
    $waiting = \App\Models\Queue::where('status', 'menunggu')->orderBy('number')->get();

    return view('/queue/display_fullscreen', compact('current', 'waiting'));
});

Route::middleware('auth')->prefix('queue')->name('queue.')->group(function () {
    Route::get('/', [QueueController::class, 'index'])->name('index');
    Route::post('/generate', [QueueController::class, 'generate'])->name('generate');
    Route::get('/call/{id}', [QueueController::class, 'call'])->name('call');
    Route::get('/receive/{id}', [QueueController::class, 'receive'])->name('receive');
    Route::get('/cancel/{id}', [QueueController::class, 'cancel'])->name('cancel');
    Route::get('/recall/{id}', [QueueController::class, 'recall'])->name('recall');
    Route::post('/call-specific', [QueueController::class, 'callSpecific'])->name('callSpecific');
});
