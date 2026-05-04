<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_detail';
    
    // Primary Key non-incrementing
    protected $primaryKey = 'detail_po_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Field yang bisa diisi. Tambahkan `status_verifikasi` untuk melacak apakah barang sudah diverifikasi/diretur
    protected $fillable = [
        'detail_po_id',
        'po_id',
        'kode_barang',
        'harga_barang_id',
        'quantity',
        'harga_satuan',
        'satuan', // ENUM
        'subtotal',
        'status_verifikasi', 
    ];
    
    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relasi ke Purchase Order
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    // Relasi ke Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
