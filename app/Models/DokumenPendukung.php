<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenPendukung extends Model
{
    use HasFactory;

    protected $table = 'dokumen_pendukung';

    protected $fillable = [
        'jenazah_id',
        'laporan_kehilangan_id',
        'jenis_dokumen',
        'path_file',
        'diunggah_oleh',
    ];

    public function jenazah()
    {
        return $this->belongsTo(Jenazah::class, 'jenazah_id');
    }

    public function laporanKehilangan()
    {
        return $this->belongsTo(LaporanKehilangan::class, 'laporan_kehilangan_id');
    }

    public function pengunggah()
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}
