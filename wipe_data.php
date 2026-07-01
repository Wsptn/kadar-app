<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    $models = [
        \App\Models\RiwayatJabatan::class,
        \App\Models\RiwayatTugas::class,
        \App\Models\RiwayatPendidikan::class,
        \App\Models\KinerjaDetail::class,
        \App\Models\Kinerja::class,
        \App\Models\Pengurus::class,
        \App\Models\Kamar::class,
        \App\Models\Daerah::class,
        \App\Models\Wilayah::class,
    ];

    foreach ($models as $model) {
        if (class_exists($model)) {
            $model::truncate();
            echo "Truncated model: $model\n";
        }
    }
    
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "Semua data terkait berhasil dikosongkan secara total!\n";
} catch (\Exception $e) {
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "Error: " . $e->getMessage() . "\n";
}
