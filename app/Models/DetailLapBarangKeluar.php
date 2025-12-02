<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLapBarangKeluar extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'detail_lap_barang_keluar';
    protected $primaryKey = 'detail_keluar_id';
    protected $fillable = [
        'lap_keluar_id',
        'kode_barang',
        'jumlah_keluar',
        'harga_jual',
        'subtotal'
    ];

    public function laporan()
    {
        return $this->belongsTo(LapBarangKeluar::class, 'lap_keluar_id', 'lap_keluar_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
