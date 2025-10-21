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
        Schema::create('barangs', function (Blueprint $table) {
            $table->string('kode_barang', 50)->primary(); // Primary Key
            $table->string('nama_barang', 255)->nullable(false);
            $table->enum('nama_satuan', ['pcs', 'renteng', 'pack', 'karton'])->nullable(false);
            // Foreign Keys
            $table->unsignedBigInteger('kategori_barang_id');
            $table->unsignedBigInteger('id_supplier');
            $table->unsignedInteger('harga_barang_id');

            // Relasi ke tabel kategori_barangs
            $table->foreign('kategori_barang_id')
                  ->references('kategori_barang_id')
                  ->on('kategori_barangs')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            // Relasi ke tabel suppliers
            $table->foreign('id_supplier')
                  ->references('id_supplier')
                  ->on('suppliers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
