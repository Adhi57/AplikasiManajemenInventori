<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'barangs';
    protected $primaryKey = 'kode_barang';
    public $incrementing = false; 
    public $timestamps = false;   

    protected $keyType = 'string'; 

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan_jual',
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


    protected static function boot()
    {
        parent::boot();
        // Removed static::creating that generated kode_barang automatically.
        // Users will now input the barcode manually.
    }

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
