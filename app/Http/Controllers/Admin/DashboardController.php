<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;

use App\Services\TagihanService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $totalPembayaran = Tagihan::where('status', 'lunas')->count();
        $totalTunggakan = Tagihan::where('status', 'belum')->count();

        $query = Tagihan::with('siswa');
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }
        $laporan = $query->orderBy('tahun', 'asc')->orderBy('bulan', 'asc')->get();

        $pembayaran = Tagihan::with('siswa')
            ->where('status', 'lunas')
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

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

    public function exportLaporan(Request $request)
    {
        $type = $request->get('type');
        $bulanAktif = $request->bulan ?? now()->month;
        $tahunAktif = $request->tahun ?? now()->year;

        // 1. Ambil dan Format Data sesuai Tipe
        if (in_array($type, ['bulanan'])) {
            $dataLaporan = $this->getLaporanBulanan($bulanAktif, $tahunAktif);
            $fileName = "Rekap_SPP_Bulan_{$bulanAktif}_{$tahunAktif}.xlsx";
        } else {
            $dataLaporan = $this->getLaporanHarian();
            $fileName = "Laporan_Harian_" . now()->format('d-m-Y') . ".xlsx";
        }

        // 2. Build Excel Spreadsheet
        $spreadsheet = $this->buildExcelSpreadsheet($dataLaporan);

        // 3. Return Download Response
        return $this->downloadExcel($spreadsheet, $fileName);
    }

    /**
     * Mengambil dan memformat data khusus laporan bulanan (Termasuk Tunggakan)
     */
    private function getLaporanBulanan($bulan, $tahun)
    {
        $tagihans = Tagihan::with('siswa')
        ->where('bulan', $bulan)
        ->where('tahun', $tahun)
        ->orderBy('status', 'asc')
        ->get();

        return $tagihans->map(function ($tagihan) {
            $isLunas = $tagihan->status === 'lunas';

            return [
                'nama'        => $tagihan->siswa->nama ?? '-',
                'tahun_masuk' => $tagihan->siswa->tahun_masuk ?? '-',
                'kelas'       => $tagihan->siswa->kelas ?? '-',
                'jurusan'     => strtoupper($tagihan->siswa->jurusan ?? '-'),
                'bulan_spp'   => $tagihan->bulan_text ?? '-',
                'metode'      => $isLunas ? strtoupper($tagihan->metode ?? '-') : '-',
                'jumlah'      => $tagihan->jumlah,
                'tanggal'     => $isLunas && $tagihan->tanggal_bayar ? Carbon::parse($tagihan->tanggal_bayar)->format('d-m-Y H:i') . ' WIB' : '-',
                'status'      => $isLunas ? 'LUNAS' : 'BELUM BAYAR',
            ];
        });
    }

    /**
     * Mengambil dan memformat data khusus laporan harian
     */
    private function getLaporanHarian()
    {
        $tagihans = Tagihan::with('siswa')
            ->where('status', 'lunas')
            ->whereDate('tanggal_bayar', Carbon::today())
            ->orderBy('tanggal_bayar', 'asc')
            ->get();

        return $tagihans->map(function ($tagihan) {
            return [
                'nama'        => $tagihan->siswa->nama ?? '-',
                'tahun_masuk' => $tagihan->siswa->tahun_masuk ?? '-',
                'kelas'       => $tagihan->siswa->kelas ?? '-',
                'jurusan'     => strtoupper($tagihan->siswa->jurusan ?? '-'),
                'bulan_spp'   => $tagihan->bulan_text ?? '-',
                'metode'      => strtoupper($tagihan->metode ?? '-'),
                'jumlah'      => $tagihan->jumlah ?? 0,
                'tanggal'     => $tagihan->tanggal_bayar ? Carbon::parse($tagihan->tanggal_bayar)->format('d-m-Y H:i') . ' WIB' : '-',
                'status'      => 'LUNAS',
            ];
        });
    }

    /**
     * Universal Excel Builder (Memisahkan logika PhpSpreadsheet dari logika Database)
     */
    private function buildExcelSpreadsheet($dataLaporan)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Headers
        $headers = ['No', 'Nama Siswa', 'Tahun Masuk', 'Kelas', 'Jurusan', 'SPP Bulan', 'Metode', 'Jumlah', 'Tanggal Bayar', 'Status'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);

        // Populate Data
        $rowNum = 2;
        foreach ($dataLaporan as $index => $row) {
            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $row['nama']);
            $sheet->setCellValue('C' . $rowNum, $row['tahun_masuk']);
            $sheet->setCellValue('D' . $rowNum, $row['kelas']);
            $sheet->setCellValue('E' . $rowNum, $row['jurusan']);
            $sheet->setCellValue('F' . $rowNum, $row['bulan_spp']);
            $sheet->setCellValue('G' . $rowNum, $row['metode']);
            $sheet->setCellValue('H' . $rowNum, $row['jumlah']);
            $sheet->setCellValue('I' . $rowNum, $row['tanggal']);
            $sheet->setCellValue('J' . $rowNum, $row['status']);

            // Styling Status Belum Bayar
            if ($row['status'] === 'BELUM BAYAR') {
                $sheet->getStyle('J' . $rowNum)->getFont()->getColor()->setARGB('FFFF0000');
            }

            $rowNum++;
        }

        // Auto-size Columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Memisahkan logika HTTP Response untuk unduhan
     */
    private function downloadExcel($spreadsheet, $fileName)
    {
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