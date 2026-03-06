<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fks = DB::select("SELECT TABLE_NAME, CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME = 'barangs' AND REFERENCED_COLUMN_NAME = 'kode_barang' AND TABLE_SCHEMA = DATABASE()");

foreach($fks as $fk) {
    echo $fk->TABLE_NAME . "|" . $fk->CONSTRAINT_NAME . "\n";
}
