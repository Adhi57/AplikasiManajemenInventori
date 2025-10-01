<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';
    protected $primaryKey = 'kode_barang';
    public $incrementing = false; // karena PK bukan auto-increment
    public $timestamps = false;   // tidak ada created_at & updated_at

    protected $keyType = 'string'; // karena PK varchar

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'nama_satuan',
        'kategori_barang_id',
        'id_supplier',
        'harga_barang_id',
    ];


    // Prefix Kombinasi untuk kode Barang
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($barang) {
            // 1. ambil Prefix
            $kategoriId = $barang->kategori_barang_id;
            $supplierId = $barang->id_supplier;
            
            // Format ID
            $prefix = 'K' . str_pad($kategoriId, 2, '0', STR_PAD_LEFT) . 
                      'S' . str_pad($supplierId, 2, '0', STR_PAD_LEFT);

            // 2. Mencari Nomer Selanjutnya
            $lastBarang = static::where('kode_barang', 'like', $prefix . '%')
                                ->orderBy('kode_barang', 'desc')
                                ->first();

            $nextNumber = 1;

            if ($lastBarang) {
                $lastNumber = (int) substr($lastBarang->kode_barang, strrpos($lastBarang->kode_barang, '-') + 1);
                $nextNumber = $lastNumber + 1;
            }

            // Kombinasi
            $sequentialPart = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            
            // Final code structure: 
            $barang->kode_barang = $prefix . '-' . $sequentialPart;
        });
    }

    // Relasi ke kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_barang_id', 'kategori_barang_id');
    }

    // Relasi ke supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    // Relasi ke harga barang (satu barang bisa punya satu harga aktif di field ini)
    public function hargaBarang()
    {
        return $this->belongsTo(hargaBarang::class, 'harga_barang_id', 'harga_barang_id');
    }

    // Relasi ke stok barang
    public function stok()
    {
        return $this->hasOne(stokBarang::class, 'kode_barang', 'kode_barang');
    }

    // Relasi ke semua riwayat harga
    public function daftarHarga()
    {
        return $this->hasMany(HargaBarang::class, 'kode_barang', 'kode_barang');
    }
}
