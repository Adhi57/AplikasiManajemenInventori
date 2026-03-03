<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use App\Models\PurchaseOrder; 
use App\Models\Barang; 

class stokBarang extends Model
{
    use HasFactory;
    protected $table = 'stok_barangs';
    
    public $incrementing = false; 

    protected $keyType = 'string';

    public $timestamps = false; 

    protected $fillable = [
        'po_id', 
        'kode_barang',
        'jumlah_stok',
        'jumlah_stok_rusak',
        'tgl_kadaluarsa',
    ];
    
    // Field yang akan diperlakukan sebagai tanggal
    protected $dates = [
        'tgl_kadaluarsa',
        'updated_at',
    ];


    public function stoks()
    {
        return $this->hasMany(StokBarang::class, 'kode_barang', 'kode_barang');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    public function getTotalStokAttribute()
    {
        return $this->stoks()->sum('jumlah_stok');
    }
}
