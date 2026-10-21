<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPendukung extends Model
{
    use HasFactory;

    public const JENIS = [
        'foto' => 'Foto',
        'sidik_jari' => 'Sidik jari',
        'surat_keterangan' => 'Surat keterangan',
        'berita_acara' => 'Berita acara',
        'identitas' => 'Dokumen identitas (KTP/KK)',
        'lainnya' => 'Lainnya',
    ];

    protected $table = 'dokumen_pendukung';

    protected $fillable = [
        'jenazah_id',
        'laporan_kehilangan_id',
        'jenis_dokumen',
        'path_file',
        'diunggah_oleh',
    ];

    // path_file adalah lokasi berkas di disk privat; tidak perlu dikirim ke peramban.
    protected $hidden = ['path_file'];

    public function jenazah(): BelongsTo
    {
        return $this->belongsTo(Jenazah::class, 'jenazah_id');
    }

    public function laporanKehilangan(): BelongsTo
    {
        return $this->belongsTo(LaporanKehilangan::class, 'laporan_kehilangan_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}
