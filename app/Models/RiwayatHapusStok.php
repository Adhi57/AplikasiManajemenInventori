<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatHapusStok extends Model
{
    protected $table = 'riwayat_hapus_stok';

    public $timestamps = false;

    protected $fillable = [
        'po_id',
        'kode_barang',
        'nama_barang',
        'jumlah_stok',
        'jumlah_stok_rusak',
        'tgl_kadaluarsa',
        'alasan',
        'dihapus_oleh',
        'created_at',
    ];

    protected $casts = [
        'tgl_kadaluarsa' => 'date',
        'created_at' => 'datetime',
    ];
}
