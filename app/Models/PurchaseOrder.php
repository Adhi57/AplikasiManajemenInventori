<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'Purchase_Orders';
    
    protected $primaryKey = 'po_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'po_id',
        'id_supplier',
        'user_id',
        'tanggal_po',
        'status', 
        'total_harga',
    ];

    protected $casts = [
        'tanggal_po' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'po_id', 'po_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->po_id)) {
                $model->po_id = self::generatePoId();
            }
        });
    }

    public static function generatePoId()
    {
        // Format bulan dan tahun 
        $prefix = 'PO_' . date('ym'); 
        $tanggalHariIni = date('Y-m-d');

        // Cari PO terakhir yang dibuat hari ini atau bulan ini
        $lastPo = self::where('po_id', 'like', $prefix . '%')
                        // Mengambil nomor urut tertinggi untuk periode bulan ini
                        ->orderBy('po_id', 'desc') 
                        ->first();

        $nomorUrut = 1;

        if ($lastPo) {
            // Ambil nomor urut dari PO ID terakhir 
            $lastNumber = (int) substr($lastPo->po_id, -4);
            $nomorUrut = $lastNumber + 1;
        }

        // Format nomor urut menjadi 4 digit 
        $newPoId = $prefix . '_' . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);

        return $newPoId;
    }
}
