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

    public function exportLaporan(Request $request)
    {
        // 1. Ambil data dengan filter bulan
        $query = Tagihan::with('siswa');
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        $laporan = $query->latest()->get();

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

        // 2. Inisialisasi PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 3. Set Header Kolom Excel (Menyesuaikan tabel Web kamu)
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

        // 4. Isi Data Looping (Mulai dari Baris 2)
        $rowNum = 2;
        foreach ($laporan as $index => $row) {
            $bulanAngka = (int)$row->bulan;
            $bulanTeks  = $listBulanIndo[$bulanAngka] ?? 'Bulan ' . $bulanAngka;

            // Ambil data metode pembayaran jika ada relasi ke tabel pembayaran, default ke '-'
            $metodeBayar = $row->pembayaran->metode ?? '-';

            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $row->siswa->nama ?? 'Tidak Diketahui');
            $sheet->setCellValue('C' . $rowNum, $row->siswa->tahun_masuk ?? 'N/A'); // Tambahan Tahun Masuk
            $sheet->setCellValue('D' . $rowNum, $row->siswa->kelas ?? 'N/A');
            $sheet->setCellValue('E' . $rowNum, strtoupper($row->siswa->jurusan ?? 'N/A'));
            $sheet->setCellValue('F' . $rowNum, $bulanTeks . ' ' . $row->tahun);
            $sheet->setCellValue('G' . $rowNum, strtoupper($metodeBayar)); // Tambahan Metode (Midtrans/Manual/dll)
            $sheet->setCellValue('H' . $rowNum, $row->jumlah);
            $sheet->setCellValue('I' . $rowNum, $row->updated_at ? $row->updated_at->format('d-m-Y H:i') : '-');
            $sheet->setCellValue('J' . $rowNum, $row->status == 'lunas' ? 'Lunas' : 'Belum Bayar');

            $rowNum++;
        }

        // 5. Otomatis Lebarkan Ukuran Kolom (A sampai J)
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 6. Streaming response download file .xlsx
        $fileName = 'Laporan_SPP_' . now()->format('d-m-Y') . '.xlsx';

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