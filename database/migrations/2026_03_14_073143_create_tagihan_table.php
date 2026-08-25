<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('siswa_nis');
            $table->foreign('siswa_nis')->references('nis')->on('siswa')->cascadeOnDelete();
            
            $table->integer('bulan');
            $table->integer('tahun');
            $table->integer('jumlah');
            
            $table->enum('status', ['belum', 'lunas'])->default('belum');
            $table->boolean('reminder_sent')->default(false);
            $table->string('metode')->nullable();
            $table->dateTime('tanggal_bayar')->nullable();
            
            $table->timestamps();

            // Indexing untuk pencarian cepat
            $table->index(['bulan', 'tahun', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
