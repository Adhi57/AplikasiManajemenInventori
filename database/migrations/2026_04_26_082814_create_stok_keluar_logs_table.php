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
        Schema::create('stok_keluar_logs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');
            $table->string('po_id')->nullable();
            $table->decimal('jumlah', 10, 2);
            $table->date('tgl_kadaluarsa')->nullable();
            $table->string('sumber')->nullable(); 
            $table->string('dieksekusi_oleh')->nullable();
            $table->timestamp('waktu')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_keluar_logs');
    }
};
