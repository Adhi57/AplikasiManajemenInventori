<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    use HasFactory;
    protected $table = 'kategori_barangs';
    protected $primaryKey = 'kategori_barang_id';
    protected $fillable = ['nama_kategori_barang'];
    public $timestamps = true;
    
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kategori_barang_id', 'kategori_barang_id');
    }
}
