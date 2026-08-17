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

    // 2. Jalankan perintah schedule:run (Untuk mengecek kalender otomatisasi seperti tgl 15)
    Artisan::call('schedule:run');
    $scheduleOutput = Artisan::output();

    // 3. Jalankan perintah queue:work (Untuk mengeksekusi tabel 'jobs' / kirim WA)
    // Menggunakan --stop-when-empty agar proses PHP tidak hang/loading selamanya
    Artisan::call('queue:work', ['--stop-when-empty' => true]);
    $queueOutput = Artisan::output();

    return response()->json([
        'status' => 'success',
        'message' => 'Scheduler & Queue berhasil dijalankan',
        'output_schedule' => $scheduleOutput,
        'output_queue' => $queueOutput
    ]);
});