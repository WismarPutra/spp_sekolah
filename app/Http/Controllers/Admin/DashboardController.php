<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Tambahkan ini agar bisa memproses filter
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Statistik Utama
        $totalSiswa = Siswa::count();
        $totalTagihan = Tagihan::count();
        $totalPembayaran = Pembayaran::where('status', 'paid')->count();
        $totalTunggakan = Tagihan::where('status', 'belum')->count();

        // 2. Query Riwayat Pembayaran (Logika dari PembayaranController)
        // Kita gunakan Tagihan agar bisa difilter bulannya
        $query = Tagihan::with('siswa');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        $laporan = $query->latest()->get();

        // 3. Data tambahan untuk modal/tabel (opsional jika masih butuh)
        $pembayaran = Pembayaran::with(['tagihan.siswa'])->latest()->take(10)->get();

        return view('admin.dashboard.index', compact(
            'totalSiswa',
            'totalTagihan',
            'totalPembayaran',
            'totalTunggakan',
            'pembayaran',
            'laporan'
        ));
    }

    // Pindahkan fungsi export ke sini juga
    public function exportLaporan(Request $request)
    {
        $fileName = 'Laporan_SPP_' . now()->format('d-m-Y') . '.csv';

        $response = new StreamedResponse(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Nama Siswa', 'Kelas', 'jurusan', 'Bulan SPP', 'Nominal', 'Status', 'Tanggal Bayar']);

            $query = Tagihan::with('siswa');
            if ($request->bulan) {
                $query->where('bulan', $request->bulan);
            }

            foreach ($query->cursor() as $index => $row) {

                // Konversi data bulan angka di database menjadi teks nama bulan
                $bulanAngka = (int)$row->bulan;
                $bulanTeks  = $listBulanIndo[$bulanAngka] ?? 'Bulan ' . $bulanAngka;

                fputcsv($handle, [
                    $index + 1,
                    $row->siswa->nama ?? 'Tidak Diketahui',
                    $row->siswa->kelas ?? 'N/A',
                    strtoupper($row->siswa->jurusan ?? 'N/A'),
                    $bulanTeks . ' ' . $row->tahun, // Hasil: Mei 2026
                    $row->jumlah,
                    $row->status == 'lunas' ? 'Lunas' : 'Belum Bayar',
                    $row->updated_at ? $row->updated_at->format('d-m-Y H:i') : '-'
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}