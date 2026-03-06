<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default values
        $defaults = [
            ['key' => 'nama_perusahaan', 'value' => 'PT. Nama Perusahaan', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'alamat_perusahaan', 'value' => 'Jl. Contoh Alamat No. 123', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'telepon_perusahaan', 'value' => '021-1234567', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'email_perusahaan', 'value' => 'info@perusahaan.com', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'logo_perusahaan', 'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ppn_persen', 'value' => '11', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'mata_uang', 'value' => 'Rp', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('settings')->insert($defaults);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
