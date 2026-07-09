<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Tambahkan ini agar bisa memproses filter
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        // 2. Query Riwayat Laporan Pembayaran (Berdasarkan Tagihan)
        $query = Tagihan::with('siswa');

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // PENGURUTAN: Urutkan berdasarkan tahun terkecil ke terbesar, lalu bulan ke-1 sampai 12
        $laporan = $query->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        // 3. Data utama untuk Tabel Riwayat Pembayaran Terbaru di halaman Web
       $pembayaran = Pembayaran::with(['tagihan.siswa'])
        ->where('status', 'paid') // Memastikan hanya menampilkan yang sudah lunas
        ->get()
        ->sortBy([
            ['tagihan.tahun', 'asc'],
            ['tagihan.bulan', 'asc']
        ])
        ->take(10);

        return view('admin.dashboard.index', compact(
            'totalSiswa',
            'totalTagihan',
            'totalPembayaran',
            'totalTunggakan',
            'pembayaran',
            'laporan'
        ));
    }

    public function exportLaporan(Request $request)
    {
        $hariIni = now();
        $bulanAktif = $hariIni->month;
        $tahunAktif = $hariIni->year;

        // 1. Inisialisasi Query Utama dan pastikan status sudah 'paid'
        $query = Pembayaran::with(['tagihan.siswa'])->where('status', 'paid');

        // 2. Cek tipe ekspor berdasarkan tombol yang diklik
        if ($request->get('type') === 'toleransi') {
            // Logika Masa Toleransi (Dari tanggal 15 jam 00:00 sampai 25 jam 23:59 di bulan berjalan)
            $tanggalMulai = \Carbon\Carbon::create($tahunAktif, $bulanAktif, 15)->startOfDay();
            $tanggalAkhir = \Carbon\Carbon::create($tahunAktif, $bulanAktif, 25)->endOfDay();

            $query->whereBetween('tanggal_bayar', [$tanggalMulai, $tanggalAkhir]);
            $fileName = 'Rekap_Bayar_Toleransi_15-25_' . $hariIni->format('F') . '_' . $tahunAktif . '.xlsx';
        } else {
            // Default: Logika Harian (Hanya transaksi tanggal hari ini saja)
            $query->whereDate('tanggal_bayar', \Carbon\Carbon::today());
            $fileName = 'Laporan_Harian_' . $hariIni->format('d-m-Y') . '.xlsx';
        }

        // Filter tambahan berdasarkan pilihan bulan jika ada di request
        if ($request->filled('bulan')) {
            $query->whereHas('tagihan', function ($q) use ($request) {
                $q->where('bulan', $request->bulan);
            });
        }

        // 3. PENGURUTAN: Mengurutkan dari tanggal bayar paling awal ke yang paling akhir (Kronologis berurutan)
        $laporan = $query->orderBy('tanggal_bayar', 'asc')->get();

        // 4. Inisialisasi PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 5. Set Header Kolom Excel
        $headers = [
            'No',
            'Nama Siswa',
            'Tahun Masuk',
            'Kelas',
            'Jurusan',
            'SPP Bulan',
            'Metode',
            'Jumlah',
            'Tanggal Bayar',
            'Status'
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Style untuk Header (Bold)
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        // 6. Isi Data Looping (Mulai dari Baris 2)
        $rowNum = 2;
        foreach ($laporan as $index => $bayar) {
            $namaSiswa   = $bayar->tagihan->siswa->nama ?? 'Data Tidak Ditemukan';
            $tahunMasuk  = $bayar->tagihan->siswa->tahun_masuk ?? '-';
            $kelas       = $bayar->tagihan->siswa->kelas ?? '-';
            $jurusan     = strtoupper($bayar->tagihan->siswa->jurusan ?? '-');
            $bulanSpp    = $bayar->tagihan->bulan_text ?? '-';
            $metodeBayar = strtoupper($bayar->metode ?? '-');
            $jumlahBayar = $bayar->jumlah ?? 0;

            $tanggalBayar = $bayar->tanggal_bayar
                ? \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d-m-Y H:i') . ' WIB'
                : '-';

            $status      = strtoupper($bayar->status ?? '-');

            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $namaSiswa);
            $sheet->setCellValue('C' . $rowNum, $tahunMasuk);
            $sheet->setCellValue('D' . $rowNum, $kelas);
            $sheet->setCellValue('E' . $rowNum, $jurusan);
            $sheet->setCellValue('F' . $rowNum, $bulanSpp);
            $sheet->setCellValue('G' . $rowNum, $metodeBayar);
            $sheet->setCellValue('H' . $rowNum, $jumlahBayar);
            $sheet->setCellValue('I' . $rowNum, $tanggalBayar);
            $sheet->setCellValue('J' . $rowNum, $status);

            $rowNum++;
        }

        // 7. Otomatis Lebarkan Ukuran Kolom (A sampai J)
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 8. Streaming response download file .xlsx
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}