<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {

            $table->id();

            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();

            $table->foreignId('admin_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('spp_id')->constrained('spp')->cascadeOnDelete();

            $table->string('bulan');

            $table->integer('jumlah');

            $table->enum('status', ['belum', 'lunas'])->default('belum');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
