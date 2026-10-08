<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPostmortem extends Model
{
    use HasFactory;

    protected $table = 'data_postmortem';

    protected $fillable = [
        'jenazah_id',
        'gigi_geligi',
        'ciri_khusus',
        'golongan_darah',
        'estimasi_tinggi_badan_cm',
        'estimasi_berat_badan_kg',
        'sidik_jari_path',
        'kode_sampel_dna',
        'diperiksa_oleh',
        'tanggal_pemeriksaan',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
    ];

    public function jenazah()
    {
        return $this->belongsTo(Jenazah::class, 'jenazah_id');
    }

    public function pemeriksa()
    {
        return $this->belongsTo(User::class, 'diperiksa_oleh');
    }
}
