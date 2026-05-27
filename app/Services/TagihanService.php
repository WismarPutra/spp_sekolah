<?php

namespace App\Services;

use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Spp;
use App\Jobs\KirimTagihanSppJob;
use Illuminate\Support\Facades\Auth;

class TagihanService
{
    protected $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function create($data)
    {
        // Tentukan list siswa
        if (isset($data['tipe_tagihan']) && $data['tipe_tagihan'] === 'massal') {
            $siswaList = Siswa::all();
        } else {
            $siswaList = Siswa::where('id', $data['siswa_id'])->get();
        }

        $namaBulan = [
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

        $anyCreated = false;

        foreach ($siswaList as $index => $siswa) {
            $menghitungJumlahTagihan = Tagihan::where('siswa_id', $siswa->id)
                ->where('bulan', $data['bulan'])
                ->where('tahun', $data['tahun'] ?? date('Y'))->count();

            if ($menghitungJumlahTagihan >= 1) {
                continue;
            }

            $sppId = null;
            $jumlah = 0;

            if (isset($data['tipe_tagihan']) && $data['tipe_tagihan'] === 'massal') {
                // Cari SPP yang cocok berdasarkan kelas dan jurusan siswa
                $sppSiswa = Spp::where('kelas', $siswa->kelas)
                    ->where('jurusan', $siswa->jurusan)
                    ->first();
                if ($sppSiswa) {
                    $sppId = $sppSiswa->id;
                    $jumlah = $sppSiswa->nominal;
                }
            } else {
                $sppId = $data['spp_id'];
                $jumlah = $data['jumlah'];
            }

            // Simpan hanya jika spp_id ditemukan untuk menghindari Integrity Constraint Violation
            if ($sppId) {
                $tagihan = Tagihan::create([
                    'user_id'  => Auth::id(),
                    'siswa_id' => $siswa->id,
                    'spp_id'   => $sppId,
                    'bulan'    => $data['bulan'],
                    'tahun'    => $data['tahun'] ?? date('Y'),
                    'jumlah'   => $jumlah,
                    'status'   => 'belum'
                ]);

                // Kirim Notifikasi WhatsApp
                if ($tagihan && $siswa->no_hp) {
                    $anyCreated = true;
                    $siswa->update([
                        'is_sent' => $siswa->is_sent + 1
                    ]);
                    $bulanTeks = $namaBulan[(int)$tagihan->bulan] ?? $tagihan->bulan;
                    $pesan = "Halo *{$siswa->nama}* 👋\n\nPemberitahuan Tagihan SPP:\n" .
                        "Bulan: *{$bulanTeks} {$tagihan->tahun}*\n" .
                        "Total: *Rp " . number_format($tagihan->jumlah, 0, ',', '.') . "*\n\n" .
                        "Mohon segera diselesaikan pembayarannya. Terima kasih 🙏";

                    KirimTagihanSppJob::dispatch([
                        'no_hp' => $siswa->no_hp,
                        'pesan' => $pesan
                    ])->delay(now()->addSeconds($index * 5));
                }
            }
        }
        return $anyCreated;
    }

    public function markAsPaid($tagihan)
    {
        // 1. Update status tagihan jadi lunas
        $tagihan->update(['status' => 'lunas']);

        // 2. Ambil data siswa
        $siswa = $tagihan->siswa;

        if ($siswa && $siswa->no_hp) {
            $pesan = "Terima kasih *{$siswa->nama}*,\n\nPembayaran SPP bulan *{$tagihan->bulan_text}* sebesar *Rp " . number_format($tagihan->jumlah, 0, ',', '.') . "* telah kami terima dan dinyatakan **LUNAS**. \n\nSimpan pesan ini sebagai bukti pembayaran digital. Terima kasih.";

            // WAJIB: Gunakan Dispatch agar masuk ke php artisan queue:work
            KirimTagihanSppJob::dispatch([
                'no_hp' => $siswa->no_hp,
                'pesan' => $pesan
            ]);
        }
    }
}