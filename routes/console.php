<?php

use Illuminate\Support\Facades\Schedule;

/**
 * Otomatisasi Tanggal 15: Generate Tagihan SPP Bulanan
 * Berjalan otomatis setiap bulan pada tanggal 15 tepat pukul 07:00 pagi
 */
Schedule::command('tagihan:generate-otomatis')
    ->monthlyOn(15, '07:00');

/**
 * Otomatisasi Tanggal 25: Kirim Reminder / Surat Panggilan Orang Tua
 * Berjalan otomatis setiap bulan pada tanggal 25 tepat pukul 07:00 pagi
 */
Schedule::command('tagihan:reminder-otomatis')
    ->monthlyOn(25, '07:00');

/**
 * Otomatisasi: Mengulang pengiriman job/antrean yang gagal (seperti WhatsApp)
 * Berjalan otomatis setiap jam
 */
Schedule::command('queue:retry all')->hourly();