<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stok_barangs', function (Blueprint $table) {
            $table->decimal('jumlah_stok', 10, 2)->change();
            $table->decimal('jumlah_stok_rusak', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('stok_barangs', function (Blueprint $table) {
            $table->integer('jumlah_stok')->change();
            $table->integer('jumlah_stok_rusak')->default(0)->change();
        });
    }
};
