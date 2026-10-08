<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKehilangan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kehilangan';

    protected $fillable = [
        'nama_pelapor',
        'no_hp_pelapor',
        'hubungan_dengan_korban',
        'nama_korban_hilang',
        'jenis_kelamin',
        'usia_perkiraan',
        'ciri_fisik',
        'tanggal_hilang',
        'lokasi_terakhir',
        'status',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_hilang' => 'date',
    ];

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function dataAntemortem()
    {
        return $this->hasOne(DataAntemortem::class, 'laporan_kehilangan_id');
    }

    public function kecocokan()
    {
        return $this->hasMany(KecocokanIdentifikasi::class, 'laporan_kehilangan_id');
    }

    public function dokumenPendukung()
    {
        return $this->hasMany(DokumenPendukung::class, 'laporan_kehilangan_id');
    }
}
