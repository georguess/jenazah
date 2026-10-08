<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAntemortem extends Model
{
    use HasFactory;

    protected $table = 'data_antemortem';

    protected $fillable = [
        'laporan_kehilangan_id',
        'gigi_geligi',
        'ciri_khusus',
        'golongan_darah',
        'tinggi_badan_cm',
        'berat_badan_kg',
        'riwayat_medis',
    ];

    public function laporanKehilangan()
    {
        return $this->belongsTo(LaporanKehilangan::class, 'laporan_kehilangan_id');
    }
}
