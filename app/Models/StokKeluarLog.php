<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokKeluarLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'kode_barang', 'po_id', 'jumlah', 'tgl_kadaluarsa', 'sumber', 'dieksekusi_oleh', 'waktu'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
