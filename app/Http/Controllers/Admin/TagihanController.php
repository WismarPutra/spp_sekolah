<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Spp;
use App\Services\WhatsAppService;
use App\Services\TagihanService;
use App\Jobs\KirimTagihanSppJob;

class TagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mengambil semua data siswa dan spp untuk kebutuhan form lain jika ada
        $siswa = Siswa::all();
        $spp = Spp::all();

        // Mengambil daftar jurusan unik langsung dari database agar filter selalu sinkron
        $daftarJurusan = Siswa::distinct()->orderBy('jurusan')->pluck('jurusan');

        // Inisialisasi query dengan eager loading relasi
        $tagihan = Tagihan::with(['siswa', 'spp'])
            ->where('status', 'belum');

        // Logika Filter Berdasarkan Bulan
        if ($request->filled('bulan')) {
            $tagihan->where('bulan', $request->bulan);
        }

        // Logika Filter Berdasarkan Jurusan menggunakan Relasi
        if ($request->filled('jurusan')) {
            $tagihan->whereHas('siswa', function ($query) use ($request) {
                $query->where('jurusan', $request->jurusan);
            });
        }

        // Eksekusi query dengan pengurutan terbaru
        $ambilDataTagihan = $tagihan->latest()->get();

        return view('admin.tagihan.index', compact([
            'siswa',
            'spp',
            'ambilDataTagihan',
            'daftarJurusan'
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, TagihanService $tagihanService)
    {
        $request->validate([
            'tipe_tagihan' => 'required|in:individu,massal',
            'bulan'        => 'required|integer|between:1,12',
            'tahun'        => 'required|integer',
        ]);

        $dataInput = $request->all();

        // LOGIKA PERBAIKAN: Jika tipe individu namun admin lupa memilih spp_id manual di form,
        // kita bantu cari otomatis di server berdasarkan kelas & jurusan si siswa!
        if ($request->tipe_tagihan === 'individu') {
            $request->validate(['siswa_id' => 'required']);

            $siswa = Siswa::findOrFail($request->siswa_id);
            $sppSiswa = Spp::where('kelas', $siswa->kelas)
                ->where('jurusan', $siswa->jurusan)
                ->first();

            // Jika form manual diisi, pakai itu. Jika kosong, pakai pencocokan otomatis
            $dataInput['spp_id'] = $request->filled('spp_id') ? $request->spp_id : ($sppSiswa ? $sppSiswa->id : null);
            $dataInput['jumlah'] = $sppSiswa ? $sppSiswa->nominal : 0;
        }

        $proses = $tagihanService->create($dataInput);

        if (!$proses) {
            $pesanError = $request->tipe_tagihan === 'individu'
                ? 'Gagal! Siswa ini sudah memiliki tagihan aktif pada periode bulan dan tahun tersebut.'
                : 'Proses Massal Selesai: Tidak ada tagihan baru ditambahkan (semua siswa sudah memiliki tagihan atau master tarif SPP belum diset).';

            return redirect()->back()->with('error', $pesanError);
        }

        return redirect()->back()->with('success', 'Tagihan transaksi berhasil dibuat & antrean pengiriman WhatsApp terjadwal.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Tambahan logika di TagihanController.php untuk method update
    public function update(Request $request, string $id, \App\Services\TagihanService $tagihanService)
    {
        $request->validate([
            'bulan'  => 'required|integer|between:1,12',
            'tahun'  => 'required|integer',
            'jumlah' => 'required|numeric',
            'status' => 'required|in:belum,lunas', // Tambahkan validasi status
        ]);

        $tagihan = Tagihan::with('siswa')->findOrFail($id);

        // 1. Cek apakah admin mengubah status dari 'belum' menjadi 'lunas'
        if ($tagihan->status === 'belum' && $request->status === 'lunas') {

            // Update rinciannya dulu sebelum ditandai lunas
            $tagihan->update([
                'bulan'  => $request->bulan,
                'tahun'  => $request->tahun,
                'jumlah' => $request->jumlah,
            ]);

            // Gunakan TagihanService yang sudah kamu buat untuk memproses kelunasan & kirim WA Lunas otomatis
            $tagihanService->markAsPaid($tagihan);

            return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil diupdate dan diset LUNAS manual. WhatsApp bukti bayar telah dikirim.');
        }

        // 2. Jika status tidak diubah (tetap 'belum') atau kondisi lainnya
        $tagihan->update([
            'bulan'  => $request->bulan,
            'tahun'  => $request->tahun,
            'jumlah' => $request->jumlah,
            'status' => $request->status,
        ]);

        return redirect()->route('tagihan.index')->with('success', 'Data rincian periode & nominal tagihan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tagihan = Tagihan::with('siswa')->findOrFail($id);
        // 2. Ambil objek siswanya
        $siswa = $tagihan->siswa;

        // 3. Cek apakah data siswa ada dan is_sent lebih dari 0
        if ($siswa && $siswa->is_sent > 0) {
            $siswa->update([
                'is_sent' => $siswa->is_sent - 1
            ]);
        }
        $tagihan->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }


    // Fungsi untuk kirim pengingat
    public function kirimReminder($id, WhatsAppService $waService)
    {
        // Ambil data tagihan beserta data siswanya
        $tagihan = Tagihan::with('siswa')->findOrFail($id);

        $tanggalDibuat = \Carbon\Carbon::parse($tagihan->created_at);
        $sudahLewat10Hari = \Carbon\Carbon::now()->gte($tanggalDibuat);

        if (!$sudahLewat10Hari) {
            return back()->with('error', 'Gagal! Tagihan ini belum melewati batas waktu 10 hari untuk dikirimkan pengingat.');
        }
        $jumlahTunggakan = Tagihan::where('siswa_id', $tagihan->siswa_id)
            ->where('status', 'belum')
            ->count();
        $listBulan = [
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

        // 2. Konversi angka bulan ke teks
        // Kita paksa ke (int) supaya angka 4 cocok dengan key di array
        $bulanIndo = $listBulan[(int)$tagihan->bulan] ?? $tagihan->bulan;

        if ($jumlahTunggakan >= 2) {
            $pesan = "⚠️ *SURAT PANGGILAN ORANG TUA* ⚠️\n\n" .
                "Yth. Orang Tua/Wali dari *{$tagihan->siswa->nama}*,\n\n" .
                "Kami menginformasikan bahwa putra/putri Bapak/Ibu memiliki tunggakan SPP selama *{$jumlahTunggakan} bulan*.\n\n" .
                "Sehubungan dengan hal tersebut, kami mengharapkan kehadiran Bapak/Ibu ke sekolah untuk berkoordinasi dengan bagian Tata Usaha. Terima kasih.";
        } else {
            $pesan = "Halo *{$tagihan->siswa->nama}* 👋\n\n" .
                "Ini adalah pengingat untuk tagihan SPP bulan *{$bulanIndo} {$tagihan->tahun}*.\n" .
                "Total: *Rp " . number_format($tagihan->jumlah, 0, ',', '.') . "*\n\n" .
                "Mohon segera melakukan pembayaran. Terima kasih 🙏";
        }

        try {
            // 2. Masukkan ke Antrean (Queue)
            // Kita gunakan dispatch untuk mengirim data ke Job
            // Delay 5 detik antar pesan sangat bagus untuk menghindari spam filter
            KirimTagihanSppJob::dispatch([
                'no_hp' => $tagihan->siswa->no_hp,
                'pesan' => $pesan
            ])->delay(now()->addSeconds(5));

            // 3. Response Sukses
            // Pesan diubah menjadi "masuk antrean" agar lebih akurat secara teknis
            $tipeNotif = $jumlahTunggakan >= 2 ? 'Surat Panggilan' : 'Pengingat';
            return back()->with('success', 'Pesan ' . $tipeNotif . ' untuk ' . $tagihan->siswa->nama . ' telah berhasil dijadwalkan masuk antrean.');
        } catch (\Exception $e) {
            // Jika gagal memasukkan ke queue (misal database queue error)
            return back()->with('error', 'Sistem antrean bermasalah: ' . $e->getMessage());
        }
    }
}