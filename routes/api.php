<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PembayaranController;

// Jangan tambahkan '/api' di dalam petik, karena sudah otomatis
Route::post('/pakasir-callback', [PembayaranController::class, 'webhook']);
Route::get('/run-scheduler-spp', function (Request $request) {
    // 1. Keamanan: Cek token rahasia
    $secretToken = 'token-rahasia-spp-123'; // Ganti dengan token yang sulit ditebak
    
    if ($request->query('token') !== $secretToken) {
        abort(403, 'Akses Ditolak');
    }

    // 2. Jalankan perintah schedule:run
    // Ini sama dengan mengetikkan 'php artisan schedule:run' di terminal
    Artisan::call('schedule:run');

    return response()->json([
        'status' => 'success',
        'message' => 'Scheduler berhasil dijalankan',
        'output' => Artisan::output()
    ]);
});