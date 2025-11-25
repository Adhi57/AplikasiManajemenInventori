<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturBarang extends Model
{
    use HasFactory;

    protected $table = 'retur_barangs';
    protected $primaryKey = 'retur_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'po_id',
        'kode_barang',
        'qty_retur',
        'alasan',
        'status_retur',
        'tanggal_retur',
    ];

    protected $casts = [
        'tanggal_retur' => 'datetime',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    public function barang()
    {
        return $this->belongsTo(\App\Models\Barang::class, 'kode_barang', 'kode_barang');
    }
}
