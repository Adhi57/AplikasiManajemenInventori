<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengirimans', function (Blueprint $table) {
            $table->id('pengiriman_id');
            $table->string('sj_id'); // relasi ke surat_jalans
            $table->string('nama_driver')->nullable();
            $table->string('no_polisi')->nullable();
            $table->string('nama_kendaraan')->nullable();
            $table->date('tanggal_pengiriman')->nullable();
            $table->date('tanggal_sampai')->nullable();
            $table->enum('status_pengiriman', ['Menunggu', 'Dalam Perjalanan', 'Terkirim', 'Dibatalkan'])->default('Menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Relasi ke surat jalan
            $table->foreign('sj_id')->references('sj_id')->on('surat_jalans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengirimans');
    }
};
