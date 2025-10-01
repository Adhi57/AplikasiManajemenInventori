<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stokBarang extends Model
{
    protected $table = 'stok_barangs';
    protected $primaryKey = 'stok_id';
    public $timestamps = false;

    protected $fillable = [
        'kode_barang',
        'jumlah_stok',
        'jumlah_stok_rusak',
        'tgl_kadaluarsa',
        'updated_at',
    ];

    // Relasi ke tabel barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
