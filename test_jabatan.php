<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jabatans = \App\Models\MasterStrukturJabatan::all();
foreach ($jabatans as $j) {
    echo $j->entitas . " - " . $j->jabatan . "\n";
}
