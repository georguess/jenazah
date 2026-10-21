<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataAntemortem extends Model
{
    use HasFactory;

    public const GOLONGAN_DARAH = ['A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O'];

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

    protected $casts = [
        'tinggi_badan_cm' => 'decimal:1',
        'berat_badan_kg' => 'decimal:1',
    ];

    public function laporanKehilangan(): BelongsTo
    {
        return $this->belongsTo(LaporanKehilangan::class, 'laporan_kehilangan_id');
    }

    /** Antemortem tidak punya kolom pencatat; kepemilikan diturunkan dari laporannya. */
    public function scopeTerlihatOleh(Builder $query, User $user): Builder
    {
        return $user->punyaPeran(User::ROLE_KELUARGA_PELAPOR)
            ? $query->whereHas('laporanKehilangan', fn (Builder $q) => $q->where('dicatat_oleh', $user->id))
            : $query;
    }
}
