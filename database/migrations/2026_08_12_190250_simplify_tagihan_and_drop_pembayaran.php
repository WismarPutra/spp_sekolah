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
        // 1. Tambah kolom ke tagihan
        Schema::table('tagihan', function (Blueprint $table) {
            $table->string('metode')->nullable()->after('status');
            $table->timestamp('tanggal_bayar')->nullable()->after('metode');
        });

        // 2. Drop tabel pembayaran
        Schema::dropIfExists('pembayaran');
    }

    public function down(): void
    {
        // 1. Kembalikan tabel pembayaran (struktur dasar)
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnDelete();
            $table->string('metode')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->string('snap_token')->nullable();
            $table->integer('jumlah')->default(0);
            $table->timestamp('tanggal_bayar')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        // 2. Hapus kolom dari tagihan
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn(['metode', 'tanggal_bayar']);
        });
    }
};
