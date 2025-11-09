<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lap_barang_keluar', function (Blueprint $table) {
            $table->id('lap_keluar_id');
            $table->string('pengiriman_id')->index(); // relasi ke pengiriman
            $table->string('sj_id')->nullable(); // optional: surat jalan id
            $table->date('tgl_keluar');
            $table->string('status')->default('Terkirim');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_lap_barang_keluar', function (Blueprint $table) {
            $table->id('detail_keluar_id');
            $table->unsignedBigInteger('lap_keluar_id');
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->decimal('qty', 10, 2);
            $table->string('satuan');
            $table->decimal('harga_jual', 15, 2)->nullable();
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('lap_keluar_id')
                  ->references('lap_keluar_id')
                  ->on('lap_barang_keluar')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_lap_barang_keluar');
        Schema::dropIfExists('lap_barang_keluar');
    }
};
