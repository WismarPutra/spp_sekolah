<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$doku = new App\Services\DokuService();
$tagihan = App\Models\Tagihan::with('siswa.user')->first();
try {
    dump($doku->createTransaction($tagihan, 'TEST-' . time()));
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
