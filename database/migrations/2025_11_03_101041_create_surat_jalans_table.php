<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->string('sj_id', 25)->primary();
            $table->string('user_id');
            $table->unsignedBigInteger('pelanggan_id');
            $table->dateTime('tanggal_surat');
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->timestamps();

            // foreign key
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('pelanggan_id')->references('pelanggan_id')->on('pelanggans')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_jalans');
    }
};
