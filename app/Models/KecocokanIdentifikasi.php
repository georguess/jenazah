<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KecocokanIdentifikasi extends Model
{
    use HasFactory;

    protected $table = 'kecocokan_identifikasi';

    protected $fillable = [
        'jenazah_id',
        'laporan_kehilangan_id',
        'metode_kecocokan',
        'skor_kecocokan',
        'status_verifikasi',
        'diverifikasi_oleh',
        'catatan_verifikasi',
        'tanggal_verifikasi',
    ];

    protected $casts = [
        'tanggal_verifikasi' => 'datetime',
        'skor_kecocokan' => 'decimal:2',
    ];

    public function jenazah()
    {
        return $this->belongsTo(Jenazah::class, 'jenazah_id');
    }

    public function laporanKehilangan()
    {
        return $this->belongsTo(LaporanKehilangan::class, 'laporan_kehilangan_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
