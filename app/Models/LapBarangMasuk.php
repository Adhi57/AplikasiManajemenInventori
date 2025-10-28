<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LapBarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'lap_barang_masuk';
    protected $primaryKey = 'barang_masuk_id';

    protected $fillable = [
        'po_id',
        'user_id',
        'supplier_id',
        'tanggal_masuk',
    ];

    // Relasi ke Purchase Order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id_supplier');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke detail barang masuk
    public function details()
    {
        return $this->hasMany(DetailLapBarangMasuk::class, 'barang_masuk_id', 'barang_masuk_id');
    }
}
