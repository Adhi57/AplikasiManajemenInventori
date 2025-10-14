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
        Schema::create('stok_barangs', function (Blueprint $table) {
            $table->increments('stok_id'); // Primary Key
            $table->string('kode_barang', 50); // Foreign Key ke tabel barang
            $table->integer('jumlah_stok')->nullable(false);
            $table->integer('jumlah_stok_rusak')->nullable(false);
            $table->dateTime('tgl_kadaluarsa')->nullable(); 
            $table->dateTime('updated_at')->nullable(false);

            // Foreign Key
            $table->foreign('kode_barang')
                  ->references('kode_barang')
                  ->on('barangs') 
                  ->onDelete('cascade') 
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_barangs');
    }
};
