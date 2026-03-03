<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Kategori_Pelanggan extends Model
{
    use HasFactory;
    protected $table = 'kategori_pelanggans';
    protected $primaryKey = 'kategori_pelanggan_id';

    protected $fillable = [
        'kategori_pelanggan_id',
        'kategori_pelanggan',
        'jumlah_diskon',
    ];

 
    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class, 'kategori_pelanggan_id');
    }

}
