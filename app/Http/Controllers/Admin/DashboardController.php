<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Services\TagihanService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function index(Request $request)
    {
        // Jalankan pemicu otomatisasi berbasis waktu login admin
        $this->cekDanTriggerOtomatisasi();

        $totalSiswa = Siswa::count();
        $totalTagihan = Tagihan::count();
        $totalPembayaran = Pembayaran::where('status', 'paid')->count();
        $totalTunggakan = Tagihan::where('status', 'belum')->count();

        $query = Tagihan::with('siswa');
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        $laporan = $query->orderBy('tahun', 'asc')->orderBy('bulan', 'asc')->get();

        $pembayaran = Pembayaran::with(['tagihan.siswa'])
            ->where('status', 'paid')
            ->get()
            ->sortBy([['created_at', 'desc']]);

        return view('admin.dashboard.index', compact(
            'totalSiswa', 'totalTagihan', 'totalPembayaran', 'totalTunggakan', 'laporan', 'pembayaran'
        ));
    }

    private function cekDanTriggerOtomatisasi()
    {
        $hariIni = Carbon::now();
        $bulanAngkaIni = (int)$hariIni->format('n'); 
        $tahunIni = $hariIni->format('Y');

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanTeks = $namaBulan[$bulanAngkaIni] ?? $hariIni->format('F');

        if ($hariIni->day == 15) {
            $this->tagihanService->generateTagihanOtomatisBulanan($bulanAngkaIni, $tahunIni, $bulanTeks);
        }

        if ($hariIni->day == 25) {
            $this->tagihanService->kirimReminderTagihanOtomatis($bulanAngkaIni, $tahunIni, $bulanTeks);
        }
    }
}