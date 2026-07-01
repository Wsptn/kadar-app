<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pengurusWilayah = \App\Models\Pengurus::whereHas('strukturJabatan', function($q) {
    $q->where('jabatan', 'Kepala Wilayah');
})->with('kamar.daerah.wilayah')->get();

echo "--- Kepala Wilayah ---\n";
foreach ($pengurusWilayah as $p) {
    $wilayah = $p->kamar->daerah->wilayah->nama_wilayah ?? 'Tidak diketahui';
    echo "Nama: {$p->nama} | Wilayah: {$wilayah}\n";
}

$pengurusDaerah = \App\Models\Pengurus::whereHas('strukturJabatan', function($q) {
    $q->where('jabatan', 'Kepala Daerah');
})->with('kamar.daerah')->get();

echo "\n--- Kepala Daerah ---\n";
foreach ($pengurusDaerah as $p) {
    $daerah = $p->kamar->daerah->nama_daerah ?? 'Tidak diketahui';
    echo "Nama: {$p->nama} | Daerah: {$daerah}\n";
}
