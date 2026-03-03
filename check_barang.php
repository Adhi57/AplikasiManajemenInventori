<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$suppliers = DB::table('barangs')->distinct()->pluck('id_supplier')->toArray();
$kategori = DB::table('barangs')->distinct()->pluck('kategori_barang_id')->toArray();

echo "Supplier IDs in barangs: " . implode(', ', $suppliers) . "\n";
echo "Kategori IDs in barangs: " . implode(', ', $kategori) . "\n";
