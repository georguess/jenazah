<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class KecocokanIdentifikasi extends Model
{
    use HasFactory;

    public const METODE = [
        'gigi' => 'Gigi geligi',
        'sidik_jari' => 'Sidik jari',
        'dna' => 'DNA',
        'ciri_khusus' => 'Ciri khusus / medis',
        'properti' => 'Properti / barang bawaan',
    ];

    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_VERIFIKASI = [
        self::STATUS_MENUNGGU => 'Menunggu verifikasi',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_DITOLAK => 'Ditolak',
    ];

    protected $table = 'kecocokan_identifikasi';

    // status_verifikasi, diverifikasi_oleh, dan tanggal_verifikasi tidak
    // fillable: hanya diisi lewat verifikasi() oleh admin.
    protected $fillable = [
        'jenazah_id',
        'laporan_kehilangan_id',
        'metode_kecocokan',
        'skor_kecocokan',
        'diajukan_oleh',
        'catatan_verifikasi',
    ];

    protected $casts = [
        'tanggal_verifikasi' => 'datetime',
        'skor_kecocokan' => 'decimal:2',
    ];

    public function jenazah(): BelongsTo
    {
        return $this->belongsTo(Jenazah::class, 'jenazah_id');
    }

    public function laporanKehilangan(): BelongsTo
    {
        return $this->belongsTo(LaporanKehilangan::class, 'laporan_kehilangan_id');
    }

    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function masihMenunggu(): bool
    {
        return $this->status_verifikasi === self::STATUS_MENUNGGU;
    }

    /** Pengajuan baru menandai jenazah sebagai "dalam proses" identifikasi. */
    public static function ajukan(array $data, User $pengaju): self
    {
        return DB::transaction(function () use ($data, $pengaju) {
            $kecocokan = self::create($data + ['diajukan_oleh' => $pengaju->id]);
            $kecocokan->jenazah()
                ->where('status_identifikasi', Jenazah::STATUS_BELUM)
                ->update(['status_identifikasi' => Jenazah::STATUS_PROSES]);

            return $kecocokan;
        });
    }

    /**
     * Verifikasi akhir oleh admin. Bila disetujui, status jenazah dan
     * laporan ikut diperbarui dalam satu transaksi agar tidak setengah jadi.
     */
    public function verifikasi(string $status, ?string $catatan, User $verifikator): void
    {
        DB::transaction(function () use ($status, $catatan, $verifikator) {
            $this->forceFill([
                'status_verifikasi' => $status,
                'catatan_verifikasi' => $catatan,
                'diverifikasi_oleh' => $verifikator->id,
                'tanggal_verifikasi' => now(),
            ])->save();

            if ($status === self::STATUS_DISETUJUI) {
                $this->jenazah()->update(['status_identifikasi' => Jenazah::STATUS_TERIDENTIFIKASI]);
                $this->laporanKehilangan()->update(['status' => LaporanKehilangan::STATUS_COCOK]);
            }
        });
    }
}
