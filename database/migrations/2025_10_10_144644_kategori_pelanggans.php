<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kategori_pelanggans', function (Blueprint $table) {
            $table->id('kategori_pelanggan_id');
            $table->enum('kategori_pelanggan', ['Retail', 'Grosir', 'Biasa'])->notNull();
            $table->decimal('jumlah_diskon', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_pelanggans');
    }
};
