<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Jenazah extends Model
{
    use HasFactory;

    public const JENIS_KELAMIN = [
        'laki-laki' => 'Laki-laki',
        'perempuan' => 'Perempuan',
        'tidak_diketahui' => 'Tidak diketahui',
    ];

    public const KONDISI = [
        'utuh' => 'Utuh',
        'sebagian' => 'Sebagian',
        'tidak_dikenali' => 'Tidak dikenali',
    ];

    public const STATUS_BELUM = 'belum_teridentifikasi';

    public const STATUS_PROSES = 'proses';

    public const STATUS_TERIDENTIFIKASI = 'teridentifikasi';

    public const STATUS_IDENTIFIKASI = [
        self::STATUS_BELUM => 'Belum teridentifikasi',
        self::STATUS_PROSES => 'Dalam proses',
        self::STATUS_TERIDENTIFIKASI => 'Teridentifikasi',
    ];

    protected $table = 'jenazah';

    // status_identifikasi sengaja TIDAK fillable: hanya berubah lewat
    // pengajuan/verifikasi kecocokan, bukan diketik petugas di formulir.
    protected $fillable = [
        'kode_jenazah',
        'tanggal_ditemukan',
        'lokasi_ditemukan',
        'jenis_kelamin',
        'perkiraan_usia',
        'kondisi_jenazah',
        'catatan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_ditemukan' => 'date:Y-m-d',
        'perkiraan_usia' => 'integer',
    ];

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function dataPostmortem(): HasOne
    {
        return $this->hasOne(DataPostmortem::class, 'jenazah_id');
    }

    public function kecocokan(): HasMany
    {
        return $this->hasMany(KecocokanIdentifikasi::class, 'jenazah_id');
    }

    public function dokumenPendukung(): HasMany
    {
        return $this->hasMany(DokumenPendukung::class, 'jenazah_id');
    }

    /** Pencarian kode/lokasi; whereLike tanpa peka huruf menjadi ILIKE di PostgreSQL. */
    public function scopeCari(Builder $query, ?string $cari): Builder
    {
        return $query->when($cari, fn (Builder $q) => $q->where(fn (Builder $q) => $q
            ->whereLike('kode_jenazah', "%{$cari}%")
            ->orWhereLike('lokasi_ditemukan', "%{$cari}%")));
    }

    /** Dipakai Policy: memakai hasil withExists() bila ada, agar daftar tidak N+1. */
    public function sudahDiperiksa(): bool
    {
        return $this->data_postmortem_exists ?? $this->dataPostmortem()->exists();
    }
}
