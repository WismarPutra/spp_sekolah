<?php

namespace App\Services;

use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Spp;
use App\Jobs\KirimTagihanSppJob;
use Illuminate\Support\Facades\Auth;

class TagihanService
{
    public function create($data)
    {
        // 1. Tentukan ruang lingkup target siswa
        if (isset($data['tipe_tagihan']) && $data['tipe_tagihan'] === 'massal') {
            $siswaList = Siswa::all();
        } else {

        
            $siswaList = Siswa::where('id', $data['siswa_id'])->get();
        }

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $anyCreated = false;

        foreach ($siswaList as $index => $siswa) {
            // Cek duplikasi: mencegah 1 siswa punya > 1 tagihan di bulan & tahun yang sama
            $cekDuplikat = Tagihan::where('siswa_id', $siswa->id)
                ->where('bulan', $data['bulan'])
                ->where('tahun', $data['tahun'])
                ->count();

            if ($cekDuplikat >= 1) {
                continue;
            }

            $sppId = null;
            $jumlah = 0;

            // Logika penetapan nominal & id tarif master SPP di sisi Backend
            if ($data['tipe_tagihan'] === 'massal') {
                $sppSiswa = Spp::where('kelas', $siswa->kelas)
                    ->where('jurusan', $siswa->jurusan)
                    ->first();
                if ($sppSiswa) {
                    $sppId = $sppSiswa->id;
                    $jumlah = $sppSiswa->nominal;
                }
            } else {
                $sppId = $data['spp_id'];
                // Ambil nominal asli dari database master SPP (Lebih Aman daripada melempar nilai input text dari Client)
                $sppMaster = Spp::find($sppId);
                $jumlah = $sppMaster ? $sppMaster->nominal : 0;
            }

            // Eksekusi penyimpanan ke tabel tagihans jika parameter relasi valid
            if ($sppId && $jumlah > 0) {
                $tagihan = Tagihan::create([
                    'user_id'  => Auth::id(),
                    'siswa_id' => $siswa->id,
                    'spp_id'   => $sppId,
                    'bulan'    => $data['bulan'],
                    'tahun'    => $data['tahun'],
                    'jumlah'   => $jumlah,
                    'status'   => 'belum'
                ]);

                if ($tagihan && $siswa->no_hp) {
                    $anyCreated = true;
                    
                    // Increment kolom counter log pengiriman
                    $siswa->increment('is_sent');

                    $bulanTeks = $namaBulan[(int)$tagihan->bulan] ?? $tagihan->bulan;
                    $pesan = "Kepada Yth Orang Tua/Wali Murid *{$siswa->nama}* 👋\n\nPemberitahuan Tagihan SPP:\n" .
                             "Bulan: *{$bulanTeks} {$tagihan->tahun}*\n" .
                             "Total: *Rp " . number_format($tagihan->jumlah, 0, ',', '.') . "*\n\n" .
                             "Mohon segera Melakukan pembayarannya. Terima kasih 🙏";

                    // Masukkan tugas ke antrean background job dengan interval delay 5 detik untuk menghindari spamming ban
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
        $tagihan->update(['status' => 'lunas']);
        $siswa = $tagihan->siswa;

        if ($siswa && $siswa->no_hp) {
            $pesan = "Terima kasih *{$siswa->nama}*,\n\nPembayaran SPP bulan *{$tagihan->bulan_text}* sebesar *Rp " . number_format($tagihan->jumlah, 0, ',', '.') . "* telah kami terima dan dinyatakan **LUNAS**. \n\nSimpan pesan ini sebagai bukti pembayaran digital. Terima kasih.";

            KirimTagihanSppJob::dispatch([
                'no_hp' => $siswa->no_hp,
                'pesan' => $pesan
            ]);
        }
    }

    public function generateTagihanOtomatisBulanan($bulanAngka, $tahun, $bulanTeks)
    {
        $sudahGenerate = Tagihan::where('bulan', $bulanAngka)
                                ->where('tahun', $tahun)
                                ->exists();

        if (!$sudahGenerate) {
            $siswas = Siswa::all();
            
            foreach ($siswas as $index => $siswa) {
                $spp = Spp::where('tahun', $siswa->tahun_masuk)
                          ->where('kelas', $siswa->kelas)
                          ->where('jurusan', $siswa->jurusan)
                          ->first();

                $nominalSpp = $spp ? $spp->nominal : 150000; 

                Tagihan::create([
                    'siswa_id' => $siswa->id,
                    'user_id'  => $siswa->user_id,
                    'spp_id'   => $spp ? $spp->id : null,
                    'bulan'    => $bulanAngka,
                    'tahun'    => $tahun,
                    'jumlah'   => $nominalSpp,
                    'status'   => 'belum',
                ]);

                $pesan = "Kepada Yth Orang Tua/Wali Murid *{$siswa->nama}* 👋\n\nPemberitahuan Tagihan SPP:\n" .
                         "Bulan: *{$bulanTeks} {$tahun}*\n" .
                         "Total: *Rp " . number_format($nominalSpp, 0, ',', '.') . "*\n\n" .
                         "Mohon segera melakukan pembayaran melalui aplikasi pembayaran SPP SMK Utama Cianjur. Terima kasih 🙏";

                KirimTagihanSppJob::dispatch([
                    'no_hp' => $siswa->no_hp,
                    'pesan' => $pesan
                ])->delay(now()->addSeconds($index * 5));
            }
        }
    }

    /**
     * Otomatisasi Tanggal 25: Kirim Reminder/Surat Panggilan Terintegrasi
     */
    public function kirimReminderTagihanOtomatis($bulanAngka, $tahun, $bulanTeks)
    {
        // 1. Ambil data tagihan bulan berjalan yang masih 'belum' lunas dan belum dikirimi reminder
        $tagihans = Tagihan::with('siswa')
            ->where('bulan', $bulanAngka)
            ->where('tahun', $tahun)
            ->where('status', 'belum')
            ->where('reminder_sent', false)
            ->get();

        foreach ($tagihans as $index => $tagihan) {
            if ($tagihan->siswa) {
                $siswa = $tagihan->siswa;

                // 2. Hitung total seluruh tunggakan aktif milik siswa ini
                $jumlahTunggakan = Tagihan::where('siswa_id', $siswa->id)
                    ->where('status', 'belum')
                    ->count();

                // 3. Pengkondisian Pesan Berdasarkan Jumlah Tunggakan
                if ($jumlahTunggakan >= 2) {
                    // Pesan Surat Panggilan Orang Tua
                    $pesan = "Kepada Yth. Orang Tua/Wali Murid dari *{$siswa->nama}* 👋\n\n" .
                        "*SURAT PANGGILAN ORANG TUA*\n\n" .
                        "Melalui pesan ini, kami menginformasikan bahwa putra/putri Bapak/Ibu memiliki tunggakan pembayaran SPP selama *{$jumlahTunggakan} bulan*.\n\n" .
                        "Sehubungan dengan hal tersebut, kami mengharapkan kehadiran Bapak/Ibu ke sekolah untuk berkoordinasi dengan bagian Tata Usaha SMK Utama Cianjur. Terima kasih.";
                } else {
                    // Pesan Pengingat Biasa
                    $pesan = "Halo Bapak/Ibu Wali Murid dari *{$siswa->nama}* 👋\n\n" .
                        "Menginfokan kembali mengenai tagihan SPP bulan *{$bulanTeks} {$tagihan->tahun}* sebesar *Rp " . number_format($tagihan->jumlah, 0, ',', '.') . "* statusnya saat ini terpantau masih belum dilakukan pelunasan.\n\n" .
                        "Mohon untuk segera menyelesaikan pembayaran sebelum batas waktu berakhir. Terima kasih atas perhatiannya 🙏";
                }

                // 4. Masukkan ke Queue Job
                KirimTagihanSppJob::dispatch([
                    'no_hp' => $siswa->no_hp,
                    'pesan' => $pesan
                ])->delay(now()->addSeconds($index * 5));

                // 5. Kunci baris data tagihan ini
                $tagihan->update(['reminder_sent' => true]);
            }
        }
    }
}