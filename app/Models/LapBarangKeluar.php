<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LapBarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'lap_barang_keluar';
    protected $primaryKey = 'lap_keluar_id';
    protected $fillable = [
        'pengiriman_id',
        'sj_id',
        'tanggal_keluar',
        'biaya_kirim',
        'diskon',
        'total_akhir',
    ];

    public function details()
    {
        return $this->hasMany(DetailLapBarangKeluar::class, 'lap_keluar_id', 'lap_keluar_id');
    }

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class, 'pengiriman_id', 'pengiriman_id');
    }
}
