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
        Schema::create('spp', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('kelas');
            $table->string('jurusan')->nullable();
            $table->integer('nominal');
            $table->timestamps();
            
            // Opsional: Pastikan tidak ada duplikat tarif untuk tahun, kelas, dan jurusan yang sama
            $table->unique(['tahun', 'kelas', 'jurusan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spp');
    }
};
