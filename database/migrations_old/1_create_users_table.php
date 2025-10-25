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
        Schema::create('users', function (Blueprint $table) {
            // Mengubah id() menjadi integer 'user_id' dan Primary Key
            $table->integer('user_id')->primary(); // PK INT
            
            // Kolom username
            $table->string('username', 50)->unique(); // VARCHAR(50) NOT NULL, unique untuk login
            
            // Kolom password (sudah di-hash, jadi CHAR(255)
            $table->string('password'); // CHAR(255) NOT NULL (string default laravel adalah VARCHAR(255))

            // Kolom nama_lengkap
            $table->string('nama_lengkap', 50); // VARCHAR(50) NOT NULL

            // Kolom role dengan ENUM
            $table->enum('role', ['Admin Gudang', 'Staff Gudang', 'Head of Depo']); // ENUM NOT NULL
            
            // Kolom tambahan standar otentikasi Laravel
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
