<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['pelanggans', 'kategori_pelanggans', 'suppliers', 'kategori_barangs', 'barangs', 'harga_barangs'];
$output = "";
foreach ($tables as $table) {
    $output .= "\n--- $table ---\n";
    try {
        $columns = DB::select("DESCRIBE $table");
        foreach ($columns as $col) {
            $output .= $col->Field . " (" . $col->Type . ")\n";
        }
    } catch (\Exception $e) {
        $output .= "Table does not exist or error: " . $e->getMessage() . "\n";
    }
}
file_put_contents('schema.txt', $output);
