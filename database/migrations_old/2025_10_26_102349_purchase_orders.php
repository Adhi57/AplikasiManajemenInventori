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
        Schema::create('purchase_orders', function (Blueprint $table) {
            // Field 9. Tabel Purchase_Order
            $table->string('po_id', 25)->primary();
            
            // Foreign Key ke users (tipe data VARCHAR(255) sesuai skema users)
            $table->string('user_id', 255); 
            
            $table->dateTime('tanggal_po');
            
            // Foreign Key ke suppliers (tipe data BIGINT UNSIGNED sesuai skema suppliers)
            $table->bigInteger('id_supplier')->unsigned(); 
            
            $table->enum('status_po', ['Pending', 'Disetujui', 'Ditolak'])->nullable();
            $table->decimal('total', 15, 2)->nullable();
            
            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('user_id')->references('user_id')->on('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_supplier')->references('id_supplier')->on('suppliers')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['id_supplier']);
        });
        Schema::dropIfExists('purchase_orders');
    }
};
