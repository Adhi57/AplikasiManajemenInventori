<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stokOpnameLogs extends Model

{
    use HasFactory;

    protected $table = 'stok_opname_logs';

    protected $fillable = [
        'stok_id',
        'stok_baik_sebelum',
        'stok_rusak_sebelum',
        'tgl_kadaluarsa_sebelum',
        'stok_baik_sesudah',
        'stok_rusak_sesudah',
        'tgl_kadaluarsa_sesudah',
        'alasan_update',
        'user_id',
    ];

    // Relasi ke stok barang
    public function stok()
    {
        return $this->belongsTo(stokBarang::class, 'stok_id');
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
