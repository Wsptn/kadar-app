<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
Illuminate\Support\Facades\Schema::dropIfExists('pengurus_tugas');
Illuminate\Support\Facades\Schema::dropIfExists('master_tugas');
Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Dropped successfully\n";
