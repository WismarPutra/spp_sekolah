<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            // 1. Hapus Foreign Key lama (karena namanya spesifik)
            $table->dropForeign(['admin_id']);

            // 2. Ubah nama kolom
            $table->renameColumn('admin_id', 'user_id');

            // 3. Tambahkan kembali Foreign Key dengan nama baru
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'admin_id');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
