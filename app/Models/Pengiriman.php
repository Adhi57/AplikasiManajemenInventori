<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengirimans'; 
    protected $primaryKey = 'pengiriman_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pengiriman_id',
        'sj_id',
        'nama_driver',
        'nama_kendaraan',
        'no_polisi',
        'tanggal_pengiriman',
        'tanggal_sampai',
        'status_pengiriman',
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class, 'sj_id', 'sj_id');
    }


}
