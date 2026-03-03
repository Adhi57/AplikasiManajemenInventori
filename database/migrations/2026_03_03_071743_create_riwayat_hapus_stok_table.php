<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_hapus_stok', function (Blueprint $table) {
            $table->id();
            $table->string('po_id')->nullable();
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->decimal('jumlah_stok', 10, 2)->default(0);
            $table->decimal('jumlah_stok_rusak', 10, 2)->default(0);
            $table->date('tgl_kadaluarsa')->nullable();
            $table->string('alasan')->default('Kadaluarsa');
            $table->string('dihapus_oleh');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_hapus_stok');
    }
};
