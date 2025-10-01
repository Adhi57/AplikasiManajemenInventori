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
        Schema::create('harga_barangs', function (Blueprint $table) {
            $table->increments('harga_barang_id'); // Primary Key
            $table->string('kode_barang', 50);     // Foreign Key ke tabel barangs
            $table->enum('tipe_harga_barang', ['Eceran', 'Grosir', 'Diskon'])->nullable(false);
            $table->decimal('harga_jual', 15, 2)->nullable(false);
            $table->decimal('harga_beli', 15, 2)->nullable(false);
            $table->dateTime('berlaku_mulai')->nullable(false);
            $table->dateTime('berlaku_sampai')->nullable(); // nullable

            // Foreign Key
            $table->foreign('kode_barang')
                  ->references('kode_barang')
                  ->on('barangs') // pastikan tabel barangs sudah ada
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_barangs');
    }
};
