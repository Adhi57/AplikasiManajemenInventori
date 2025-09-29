<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    // Nama tabel sesuai skema Anda: Purchase_Order
    protected $table = 'Purchase_Order';
    
    // Primary Key non-incrementing
    protected $primaryKey = 'po_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // Field yang bisa diisi
    protected $fillable = [
        'po_id',
        'user_id',
        'tanggal_po',
        'supplier_id',
        'status', // ENUM('Pending', 'Disetujui', 'Ditolak')
        'total',
    ];

    // Casts untuk tipe data
    protected $casts = [
        'tanggal_po' => 'datetime',
        'total' => 'decimal:2',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Relasi ke detail Purchase Order
    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'po_id', 'po_id');
    }
}
