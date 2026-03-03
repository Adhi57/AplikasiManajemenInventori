<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class HargaBarang extends Model
{
    use HasFactory;
    protected $table = 'harga_barangs';
    protected $primaryKey = 'harga_barang_id';
    public $timestamps = false; // tidak ada created_at & updated_at

    protected $fillable = [
        'kode_barang',
        'tipe_harga_barang',
        'harga_jual',
        'harga_beli',
        'berlaku_mulai',
        'berlaku_sampai',
    ];

    // Relasi ke tabel barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
