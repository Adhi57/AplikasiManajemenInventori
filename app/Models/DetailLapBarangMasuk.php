<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLapBarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'detail_lap_barang_masuk';
    protected $primaryKey = 'detail_masuk_id';

    protected $fillable = [
        'barang_masuk_id',
        'kode_barang',
        'quantity_po',
        'quantity_diterima',
        'quantity_rusak',
        'satuan',
        'kondisi',
        'harga_satuan',
        'subtotal',
    ];

    // Relasi ke header laporan barang masuk
    public function lapBarangMasuk()
    {
        return $this->belongsTo(LapBarangMasuk::class, 'barang_masuk_id', 'barang_masuk_id');
    }

    // Relasi ke barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
