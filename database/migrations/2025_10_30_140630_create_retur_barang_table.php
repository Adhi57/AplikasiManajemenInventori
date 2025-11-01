<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retur_barangs', function (Blueprint $table) {
            $table->bigIncrements('retur_id');
            $table->string('po_id', 25);
            $table->string('kode_barang', 50);
            $table->integer('jumlah_retur')->default(0);
            $table->text('alasan_retur')->nullable();
            $table->enum('status_retur', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->dateTime('tanggal_retur')->useCurrent();
            $table->timestamps();

            // Foreign key hanya ke purchase_orders
            $table->foreign('po_id')
                  ->references('po_id')
                  ->on('purchase_orders')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retur_barangs');
    }
};
