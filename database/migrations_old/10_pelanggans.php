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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id('pelanggan_id');
            $table->string('nama_pelanggan', 40);
            $table->string('alamat', 255);
            $table->string('NPWP', 16);
            $table->string('PIC', 40);
            $table->unsignedBigInteger('kategori_pelanggan_id');
            $table->enum('tipe_harga', ['eceran', 'grosir', 'diskon']);
            $table->timestamps();

            // Relasi Foreign Key
            $table->foreign('kategori_pelanggan_id')
                  ->references('kategori_pelanggan_id') // CORRECTED LINE
                  ->on('kategori_pelanggans')
                  ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
