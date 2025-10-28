<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Barang extends Model
{
    protected $table = 'barangs';
    protected $primaryKey = 'kode_barang';
    public $incrementing = false; // karena PK bukan auto-increment
    public $timestamps = false;   

    protected $keyType = 'string'; // karena PK varchar

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan_terkecil',
        'kategori_barang_id',
        'id_supplier',
        'jml_barang_per_karton',
        'foto_produk',
        'tipe_harga_barang',
        'harga_jual',
        'harga_beli',
        'berlaku_mulai',
        'berlaku_sampai',
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


    // Relasi ke stok barang
    public function stok()
    {
        return $this->hasOne(stokBarang::class, 'kode_barang', 'kode_barang');
    }

    public function stoks()
    {
        return $this->hasMany(StokBarang::class, 'kode_barang', 'kode_barang');
    }

    public function getTotalStokAttribute()
    {
        // Sum the 'jumlah_stok' column for this item across all related rows in stok_barangs
        return $this->stoks()->sum('jumlah_stok');
    }
    public function getTotalStokRusakAttribute()
    {
        return $this->stoks()->sum('jumlah_stok_rusak');
    }

}
