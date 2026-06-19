<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fk = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'penilaian_kinerja' AND TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME = 'pengurus'");
print_r($fk);
