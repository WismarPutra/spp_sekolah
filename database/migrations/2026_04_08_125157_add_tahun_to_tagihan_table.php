<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            // Menambahkan kolom tahun (tipe string atau year)
            // Kita letakkan setelah kolom 'bulan' agar rapi
            $table->string('tahun', 4)->after('bulan');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
    }
};
