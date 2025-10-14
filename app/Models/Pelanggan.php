<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pelanggan extends Model
{
    protected $table = 'pelanggans';
    
    protected $primaryKey = 'pelanggan_id';
    protected $fillable = [
        'pelanggan_id',
        'nama_pelanggan',
        'alamat',
        'NPWP',
        'PIC',
        'kategori_pelanggan_id',
        'tipe_harga',
    ];

    // Relasi ke tabel kategori pelanggan
    public function kategori_pelanggan(): BelongsTo {
        return $this->belongsTo (Kategori_Pelanggan::class, );
    }
}
