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
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->string('detail_po_id', 50)->primary();
            
            // Foreign Key ke purchase_orders
            $table->string('po_id', 25);
            
            // Foreign Key ke barangs (tipe data VARCHAR(50) sesuai skema barangs)
            $table->string('kode_barang', 50); 
                        
            $table->integer('quantity')->nullable();
            $table->decimal('harga_satuan', 15, 2);
            $table->enum('satuan', ['pcs', 'renteng', 'pack', 'karton']);
            $table->decimal('subtotal', 15, 2);
            
            // Tambahan untuk tracking kadaluarsa batch (opsional, tapi disarankan untuk fitur batch)
            $table->dateTime('tgl_kadaluarsa_batch')->nullable();

            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('po_id')->references('po_id')->on('purchase_orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('kode_barang')->references('kode_barang')->on('barangs')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropForeign(['po_id']);
            $table->dropForeign(['kode_barang']);
        });
        Schema::dropIfExists('purchase_order_details');
    }
};
