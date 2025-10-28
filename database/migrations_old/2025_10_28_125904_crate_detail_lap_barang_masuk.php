<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_lap_barang_masuk', function (Blueprint $table) {
            $table->id('detail_masuk_id');
            $table->unsignedBigInteger('barang_masuk_id');
            $table->foreign('barang_masuk_id')->references('barang_masuk_id')->on('lap_barang_masuk')->onDelete('cascade');
            
            $table->string('kode_barang', 50);
            $table->foreign('kode_barang')->references('kode_barang')->on('barang')->onDelete('cascade');

            $table->integer('quantity_po');
            $table->integer('quantity_diterima');
            $table->integer('quantity_rusak')->default(0); // ✅ tambahan kolom baru
            $table->enum('satuan', ['pcs', 'renteng', 'pack', 'karton']);
            $table->enum('kondisi', ['Baik', 'Rusak'])->default('Baik');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_lap_barang_masuk');
    }
};