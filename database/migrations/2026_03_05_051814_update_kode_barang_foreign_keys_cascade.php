<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $fks = DB::select("SELECT TABLE_NAME, CONSTRAINT_NAME 
                           FROM information_schema.KEY_COLUMN_USAGE 
                           WHERE REFERENCED_TABLE_NAME = 'barangs' 
                           AND REFERENCED_COLUMN_NAME = 'kode_barang' 
                           AND TABLE_SCHEMA = DATABASE()");

        foreach ($fks as $fk) {
            $table = $fk->TABLE_NAME;
            $constraint = $fk->CONSTRAINT_NAME;
            
            // Drop old
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
            // Add new with CASCADE
            DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`kode_barang`) REFERENCES `barangs`(`kode_barang`) ON UPDATE CASCADE");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $fks = DB::select("SELECT TABLE_NAME, CONSTRAINT_NAME 
                           FROM information_schema.KEY_COLUMN_USAGE 
                           WHERE REFERENCED_TABLE_NAME = 'barangs' 
                           AND REFERENCED_COLUMN_NAME = 'kode_barang' 
                           AND TABLE_SCHEMA = DATABASE()");

        foreach ($fks as $fk) {
            $table = $fk->TABLE_NAME;
            $constraint = $fk->CONSTRAINT_NAME;
            
            // Drop old
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
            // Add new without CASCADE
            DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`kode_barang`) REFERENCES `barangs`(`kode_barang`)");
        }
    }
};
