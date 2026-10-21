<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LaporanKehilangan extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_COCOK = 'cocok';

    public const STATUS = [
        self::STATUS_AKTIF => 'Aktif',
        self::STATUS_COCOK => 'Cocok',
        'ditutup' => 'Ditutup',
    ];

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
        'tanggal_hilang' => 'date:Y-m-d',
        'usia_perkiraan' => 'integer',
    ];

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function dataAntemortem(): HasOne
    {
        return $this->hasOne(DataAntemortem::class, 'laporan_kehilangan_id');
    }

    public function kecocokan(): HasMany
    {
        return $this->hasMany(KecocokanIdentifikasi::class, 'laporan_kehilangan_id');
    }

    public function dokumenPendukung(): HasMany
    {
        return $this->hasMany(DokumenPendukung::class, 'laporan_kehilangan_id');
    }

    /**
     * Penyaringan data dilakukan di kueri (bukan di React):
     * keluarga pelapor hanya menerima laporan yang ia catat sendiri.
     */
    public function scopeTerlihatOleh(Builder $query, User $user): Builder
    {
        return $user->punyaPeran(User::ROLE_KELUARGA_PELAPOR)
            ? $query->where('dicatat_oleh', $user->id)
            : $query;
    }

    public function scopeCari(Builder $query, ?string $cari): Builder
    {
        return $query->when($cari, fn (Builder $q) => $q->where(fn (Builder $q) => $q
            ->whereLike('nama_korban_hilang', "%{$cari}%")
            ->orWhereLike('nama_pelapor', "%{$cari}%")));
    }

    public function milik(User $user): bool
    {
        return $this->dicatat_oleh === $user->id;
    }
}
