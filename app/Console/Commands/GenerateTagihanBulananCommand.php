<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TagihanService;
use Carbon\Carbon;

class GenerateTagihanBulananCommand extends Command
{
    protected $signature = 'tagihan:generate-otomatis';
    protected $description = 'Generate tagihan bulanan dan kirim WA tanggal 15';

    protected $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        parent::__construct();
        $this->tagihanService = $tagihanService;
    }

    public function handle()
    {
        $hariIni = Carbon::now();
        $bulanAngkaIni = (int)$hariIni->format('n'); 
        $tahunIni = $hariIni->format('Y');

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $bulanTeks = $namaBulan[$bulanAngkaIni] ?? $hariIni->format('F');

        $this->tagihanService->generateTagihanOtomatisBulanan($bulanAngkaIni, $tahunIni, $bulanTeks);
        $this->info('Proses otomatisasi tagihan bulanan tanggal 15 berhasil dijalankan.');
    }
}