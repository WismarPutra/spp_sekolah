<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PembayaranController;

// Jangan tambahkan '/api' di dalam petik, karena sudah otomatis
Route::post('/doku-callback', [PembayaranController::class, 'webhook']);