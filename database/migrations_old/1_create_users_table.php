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
        $table->string('user_id')->primary();       // ID custom
        $table->string('nama_lengkap');
        $table->string('username')->unique();
        $table->string('jabatan')->nullable();
        $table->string('email')->unique();
        $table->string('password');
        $table->enum('role', ['SuperAdmin', 'Admin', 'Head', 'Staff'])->default('Staff');
        $table->rememberToken();
        $table->timestamps();
    });
}

};
