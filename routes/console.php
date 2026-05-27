<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Inspiring;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use App\Models\Spp;
use App\Jobs\KirimTagihanSppJob;

// JADWAL OTOMATIS: DIEKSEKUSI SETIAP TANGGAL 15 JAM 06:00 PAGI
Schedule::call(function () {
    \Log::info('--- MEMULAI PROSES GENERATE TAGIHAN BULANAN OTOMATIS ---');

    // 1. Ambil data Admin secara otomatis untuk relasi user_id
    $admin = User::where('role', 'admin')->first();
    if (!$admin) {
        \Log::error('Scheduler Batal: Data Admin tidak ditemukan.');
        return;
    }

    // 2. AMAN: Reset seluruh is_sent siswa ke 0 sebelum generate bulan baru dimulai
    Siswa::query()->update(['is_sent' => 0]);
    \Log::info('Status is_sent seluruh siswa berhasil di-reset ke 0.');

    // 3. Ambil ulang semua siswa yang siap diproses
    $siswaList = Siswa::all();

    // SINKRONISASI FORMAT: Gunakan angka (1-12) dan Tahun 4 digit
    $bulanAngka = (int)now()->format('n'); // Menghasilkan: 5 (untuk Mei)
    $tahunIni   = now()->format('Y');     // Menghasilkan: 2026

    // Array teks untuk pesan WhatsApp
    $listBulanIndo = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    $bulanTeks = $listBulanIndo[$bulanAngka] ?? now()->translatedFormat('F');

    $delayPerSiswa = rand(10, 20);

    foreach ($siswaList as $key => $siswa) {

        // 4. Proteksi Double-Billing: Cek apakah siswa sudah punya tagihan di periode ini
        $cekTagihan = Tagihan::where('siswa_id', $siswa->id)
            ->where('bulan', $bulanAngka)
            ->where('tahun', $tahunIni)
            ->exists();

        if ($cekTagihan) {
            continue; // Lewati jika sudah ada (mencegah duplikasi data)
        }

        // 5. Cari tarif SPP yang sesuai profil kelas dan jurusan siswa
        $dataSpp = Spp::where('jurusan', $siswa->jurusan)
            ->where('kelas', $siswa->kelas)
            ->first(); // Hapus where('tahun') jika tarif SPP tidak pakai kolom tahun

        if ($dataSpp) {
            // 6. Buat record Tagihan di database (Format bulan berupa ANGKA)
            Tagihan::create([
                'siswa_id' => $siswa->id,
                'user_id'  => $admin->id,
                'spp_id'   => $dataSpp->id,
                'bulan'    => $bulanAngka, // Tersimpan angka (contoh: 5)
                'tahun'    => $tahunIni,
                'jumlah'   => $dataSpp->nominal,
                'status'   => 'belum',
            ]);

            // 7. Update status counter kirim siswa menjadi 1
            $siswa->update(['is_sent' => 1]);

            // 8. Susun pesan WhatsApp pemberitahuan tagihan baru
            $pesan = "*PEMBAYARAN SPP SMK UTAMA CIANJUR*\n\n" .
                "Halo, *{$siswa->nama}* 👋\n" .
                "Tagihan SPP bulan *{$bulanTeks} {$tahunIni}* telah terbit.\n\n" .
                "*Rincian:* \n" .
                "• Nominal: Rp " . number_format($dataSpp->nominal, 0, ',', '.') . "\n" .
                "• Status: *Belum Bayar*\n\n" .
                "Mohon segera lakukan pembayaran via aplikasi online. Terima kasih 🙏";

            // 9. Kirim ke Queue Job dengan jeda waktu teratur
            KirimTagihanSppJob::dispatch([
                'no_hp' => $siswa->no_hp,
                'pesan' => $pesan
            ])->delay(now()->addSeconds(($key + 1) * $delayPerSiswa));
        }
    }

    \Log::info('--- PROSES GENERATE TAGIHAN BULANAN SELESAI SINKRON ---');
})->monthlyOn(15, '06:00');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');