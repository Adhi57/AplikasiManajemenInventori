<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = [
    'Purchase_Order_Detail',
];

foreach ($tables as $t) {
    $cols = Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `$t`");
    echo "$t:\n";
    foreach ($cols as $col) {
        echo "  {$col->Field} ({$col->Type}) {$col->Null} {$col->Key}\n";
    }
    echo "\n";
}
