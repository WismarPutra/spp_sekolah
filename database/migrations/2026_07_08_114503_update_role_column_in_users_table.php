<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Ubah dulu struktur enum agar menerima 'admin', 'user', dan 'orang_tua' sementara waktu
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'orang_tua') DEFAULT 'orang_tua'");

            // 2. Update data lama yang rolenya 'user' menjadi 'orang_tua'
            DB::table('users')->where('role', 'user')->update(['role' => 'orang_tua']);

            // 3. Ubah kembali struktur enum untuk menghapus opsi 'user', sehingga menyisakan 'admin' dan 'orang_tua'
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'orang_tua') DEFAULT 'orang_tua'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan ke struktur awal jika di-rollback
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'orang_tua') DEFAULT 'user'");
            DB::table('users')->where('role', 'orang_tua')->update(['role' => 'user']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') DEFAULT 'user'");
        });
    }
};