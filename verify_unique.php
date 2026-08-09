<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$indexes = DB::select("SHOW INDEX FROM siswa WHERE Key_name = 'siswa_user_id_unique'");
echo "Unique constraint exists? " . (count($indexes) > 0 ? "Yes" : "No") . PHP_EOL;

// Check model method
$user = new App\Models\User();
echo "Method siswa() exists on User? " . (method_exists($user, 'siswa') ? "Yes" : "No") . PHP_EOL;
