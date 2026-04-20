<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangAuditLog extends Model
{
    public $timestamps = false;
    protected $table = 'barang_audit_logs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'aksi',
        'user_id',
        'user_nama',
        'data_lama',
        'data_baru',
        'kolom_berubah',
        'keterangan',
        'waktu',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
        'kolom_berubah' => 'array',
        'waktu' => 'datetime',
    ];

    /**
     * Log perubahan barang secara otomatis.
     */
    public static function catat(string $aksi, $barang, ?array $dataLama = null, ?array $dataBaru = null, ?string $keterangan = null)
    {
        $user = auth()->user();

        // Hitung kolom yang berubah
        $kolomBerubah = null;
        if ($dataLama && $dataBaru) {
            $changed = [];
            foreach ($dataBaru as $key => $value) {
                if (isset($dataLama[$key]) && $dataLama[$key] != $value) {
                    $changed[] = $key;
                }
            }
            $kolomBerubah = !empty($changed) ? $changed : null;
        }

        return self::create([
            'kode_barang' => $barang->kode_barang ?? ($dataLama['kode_barang'] ?? '-'),
            'nama_barang' => $barang->nama_barang ?? ($dataLama['nama_barang'] ?? '-'),
            'aksi' => $aksi,
            'user_id' => $user?->user_id,
            'user_nama' => $user?->nama_lengkap,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'kolom_berubah' => $kolomBerubah,
            'keterangan' => $keterangan,
        ]);
    }
}
