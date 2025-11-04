<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_jalan_details', function (Blueprint $table) {
            $table->string('detail_sj_id', 50)->primary();
            $table->string('sj_id', 25);
            $table->string('kode_barang', 50);
            $table->unsignedBigInteger('harga_barang_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('harga_satuan', 15, 2);
            $table->enum('satuan', ['pcs', 'renteng', 'pack', 'karton']);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            // foreign keys
            $table->foreign('sj_id')->references('sj_id')->on('surat_jalans')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('kode_barang')->references('kode_barang')->on('barangs')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_details');
    }
};
