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
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->renameColumn('doku_order_id', 'midtrans_order_id');
            $table->renameColumn('payment_url', 'snap_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->renameColumn('midtrans_order_id', 'doku_order_id');
            $table->renameColumn('snap_token', 'payment_url');
        });
    }
};
