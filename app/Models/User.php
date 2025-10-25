<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    
    // Sesuaikan fillable dengan kolom di tabel Anda
    protected $fillable = [
        'user_id',
        'username',
        'nama_lengkap',
        'password',
        'role',
    ];

    // ... (hidden, casts)
    
    // Agar otentikasi menggunakan kolom 'username'
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    public function getAuthIdentifierName()
    {
        return 'user_id'; // Laravel menggunakan user_id untuk session
    }
}