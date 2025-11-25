<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lap_barang_keluar', function (Blueprint $table) {
            $table->id('lap_keluar_id');
            $table->string('pengiriman_id')->index(); 
            $table->string('sj_id')->nullable();
            $table->date('tanggal_keluar');
            $table->string('status')->default('Terkirim');
            $table->text('catatan')->nullable();
            $table->decimal('biaya_kirim', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_akhir', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('detail_lap_barang_keluar', function (Blueprint $table) {
            $table->id('detail_keluar_id');
            $table->unsignedBigInteger('lap_keluar_id');
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->decimal('jumlah_keluar', 10, 2);
            $table->string('satuan');
            $table->decimal('harga_jual', 15, 2)->nullable();
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('lap_keluar_id')
                  ->references('lap_keluar_id')
                  ->on('lap_barang_keluar')
                  ->onDelete('cascade');
            $table->foreign('kode_barang')
                  ->references('kode_barang')
                  ->on('barangs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_lap_barang_keluar');
        Schema::dropIfExists('lap_barang_keluar');
    }
};
