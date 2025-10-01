<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barangs';
    protected $primaryKey = 'kategori_barang_id';
    public $incrementing = false; // karena PK bukan auto-increment
    public $timestamps = false;   // tidak ada created_at & updated_at
    protected $keyType = 'string'; // karena PK varchar

    protected $fillable = [
        'kategori_barang_id',
        'nama_kategori_barang',
    ];

    // Relasi ke barang
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kategori_barang_id', 'kategori_barang_id');
    }
}
