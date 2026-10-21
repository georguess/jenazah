<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_PETUGAS_FORENSIK = 'petugas_forensik';

    public const ROLE_PETUGAS_ADMINISTRASI = 'petugas_administrasi';

    public const ROLE_KELUARGA_PELAPOR = 'keluarga_pelapor';

    /** Daftar nilai sah kolom `role` (dipakai Form Request, middleware, dan migrasi CHECK). */
    public const DAFTAR_ROLE = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_PETUGAS_FORENSIK => 'Petugas Forensik',
        self::ROLE_PETUGAS_ADMINISTRASI => 'Petugas Administrasi',
        self::ROLE_KELUARGA_PELAPOR => 'Keluarga Pelapor',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Benar bila peran pengguna termasuk salah satu yang disebut. */
    public function punyaPeran(string ...$peran): bool
    {
        return in_array($this->role, $peran, true);
    }

    public function labelPeran(): string
    {
        return self::DAFTAR_ROLE[$this->role] ?? $this->role;
    }

    /**
     * Akun yang pernah mencatat jenazah, memeriksa postmortem, mengajukan
     * kecocokan, atau mengunggah dokumen adalah bagian jejak audit dan tidak
     * boleh dihapus (FK RESTRICT). Memakai hasil withExists() bila tersedia.
     */
    public function punyaJejakAudit(): bool
    {
        foreach (['jenazahDicatat', 'pemeriksaanPostmortem', 'kecocokanDiajukan', 'dokumenDiunggah'] as $relasi) {
            $kolom = Str::snake($relasi).'_exists';
            if ($this->{$kolom} ?? $this->{$relasi}()->exists()) {
                return true;
            }
        }

        return false;
    }

    public function jenazahDicatat(): HasMany
    {
        return $this->hasMany(Jenazah::class, 'dicatat_oleh');
    }

    public function laporanDicatat(): HasMany
    {
        return $this->hasMany(LaporanKehilangan::class, 'dicatat_oleh');
    }

    public function pemeriksaanPostmortem(): HasMany
    {
        return $this->hasMany(DataPostmortem::class, 'diperiksa_oleh');
    }

    public function kecocokanDiajukan(): HasMany
    {
        return $this->hasMany(KecocokanIdentifikasi::class, 'diajukan_oleh');
    }

    public function verifikasiKecocokan(): HasMany
    {
        return $this->hasMany(KecocokanIdentifikasi::class, 'diverifikasi_oleh');
    }

    public function dokumenDiunggah(): HasMany
    {
        return $this->hasMany(DokumenPendukung::class, 'diunggah_oleh');
    }
}
