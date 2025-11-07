<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $primaryKey = 'sj_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sj_id',
        'user_id',
        'pelanggan_id',
        'tanggal_surat',
        'status',
        'biaya_pengiriman',   
        'diskon_pelanggan',   
        'subtotal', 
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    // Relasi ke Detail Surat Jalan
    public function details()
    {
        return $this->hasMany(SuratJalanDetail::class, 'sj_id', 'sj_id');
    }

    // Relasi ke Pengiriman
    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'sj_id', 'sj_id');
    }

}
