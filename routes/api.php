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

    // MENCEGAH TABRAKAN: Gunakan Lock agar cron-job.org tidak menjalankan proses ganda di waktu bersamaan
    $lock = \Illuminate\Support\Facades\Cache::lock('scheduler_spp_lock', 60);

    if ($lock->get()) {
        try {
            // 2. Jalankan perintah schedule:run
            Artisan::call('schedule:run');
            $scheduleOutput = Artisan::output();

            // 3. Jalankan perintah queue:work
            // Menggunakan --stop-when-empty agar berhenti jika kosong
            // Menggunakan --max-time=45 agar worker mati otomatis sebelum server timeout (biasanya max 60s)
            Artisan::call('queue:work', [
                '--stop-when-empty' => true,
                '--max-time' => 45 
            ]);
            $queueOutput = Artisan::output();

            return response()->json([
                'status' => 'success',
                'message' => 'Scheduler & Queue berhasil dijalankan',
                'output_schedule' => $scheduleOutput,
                'output_queue' => $queueOutput
            ]);
        } finally {
            $lock->release(); // Lepaskan gembok
        }
    } else {
        return response()->json([
            'status' => 'warning',
            'message' => 'Proses antrean sebelumnya masih berjalan, harap tunggu hit berikutnya.'
        ]);
    }
});