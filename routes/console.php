<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\Spp;
use App\Jobs\KirimTagihanSppJob;
use Illuminate\Support\Facades\Schedule;
// RESET BULANAN
Schedule::call(function () {
    Siswa::query()->update(['is_sent' => false]);
    \Log::info('RESET BULAN BARU');
})->monthlyOn(15, '00:30');


Schedule::call(function () {
    // 1. Ambil data Admin secara otomatis dari database
    $admin = User::where('role', 'admin')->first();

    if (!$admin) {
        return; // Berhenti jika tidak ada admin
    }

    // 2. Ambil siswa yang belum dikirimi tagihan bulan ini
    $siswaList = Siswa::where('is_sent', false)->get();
    $tahunIni = now()->format('Y');
    $bulanIni = now()->translatedFormat('F'); // Format: Mei

    $delayPerSiswa = rand(10, 20); // Mengambil angka acak 10-20 detik
    foreach ($siswaList as $key => $siswa) {
        // 3. Cari tarif SPP yang sesuai dengan profil siswa
        $dataSpp = Spp::where('jurusan', $siswa->jurusan)
            ->where('kelas', $siswa->kelas)
            ->where('tahun', $tahunIni)
            ->first();

        if ($dataSpp) {
            // 4. Buat record Tagihan di database[cite: 1]
            Tagihan::create([
                'siswa_id' => $siswa->id,
                'user_id'  => $admin->id,
                'spp_id'   => $dataSpp->id,
                'bulan'    => $bulanIni,
                'tahun'    => $tahunIni,
                'jumlah'   => $dataSpp->nominal,
                'status'   => 'belum',
            ]);

            // 5. Update status siswa agar tidak diproses ulang oleh scheduler[cite: 1]
            $siswa->update(['is_sent' => true]);

            // 6. Susun pesan WhatsApp yang rapi
            $pesan = "*PEMBAYARAN SPP SMK UTAMA CIANJUR*\n\n" .
                "Halo, *{$siswa->nama}*\n" .
                "Tagihan SPP bulan *{$bulanIni} {$tahunIni}* telah terbit.\n\n" .
                "*Rincian:* \n" .
                "• Nominal: Rp " . number_format($dataSpp->nominal, 0, ',', '.') . "\n" .
                "• Status: *Belum Bayar*\n\n" .
                "Silakan lakukan pembayaran. Terima kasih.";

            // 7. Kirim ke Queue dengan Delay bertahap[cite: 1]
            // Siswa ke-1: delay 10s, Siswa ke-2: delay 20s, dst.
            KirimTagihanSppJob::dispatch([
                'no_hp' => $siswa->no_hp,
                'pesan' => $pesan
            ])->delay(now()->addSeconds(($key + 1) * $delayPerSiswa));
        }
    }
})->monthlyOn(15, '06:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');