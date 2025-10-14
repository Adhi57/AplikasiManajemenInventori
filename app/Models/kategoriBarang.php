<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barangs';
    protected $primaryKey = 'kategori_barang_id';
    public $incrementing = false; 
    public $timestamps = false;  
    protected $keyType = 'string'; 

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
