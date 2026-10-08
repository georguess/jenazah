<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenazah extends Model
{
    use HasFactory;

    protected $table = 'jenazah';

    protected $fillable = [
        'kode_jenazah',
        'tanggal_ditemukan',
        'lokasi_ditemukan',
        'jenis_kelamin',
        'perkiraan_usia',
        'kondisi_jenazah',
        'status_identifikasi',
        'catatan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_ditemukan' => 'date',
    ];

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function dataPostmortem()
    {
        return $this->hasOne(DataPostmortem::class, 'jenazah_id');
    }

    public function kecocokan()
    {
        return $this->hasMany(KecocokanIdentifikasi::class, 'jenazah_id');
    }

    public function dokumenPendukung()
    {
        return $this->hasMany(DokumenPendukung::class, 'jenazah_id');
    }
}
