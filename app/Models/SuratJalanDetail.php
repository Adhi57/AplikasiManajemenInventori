<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalanDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_sj_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'detail_sj_id',
        'sj_id',
        'kode_barang',
        'harga_barang_id',
        'quantity',
        'harga_satuan',
        'satuan',
        'subtotal',
    ];

    // Relasi ke Surat Jalan
    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'sj_id', 'sj_id');
    }

    // Relasi ke Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
