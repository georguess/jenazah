<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_PETUGAS_FORENSIK = 'petugas_forensik';
    public const ROLE_PETUGAS_ADMINISTRASI = 'petugas_administrasi';
    public const ROLE_KELUARGA_PELAPOR = 'keluarga_pelapor';

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

    public function jenazahDicatat()
    {
        return $this->hasMany(Jenazah::class, 'dicatat_oleh');
    }

    public function pemeriksaanPostmortem()
    {
        return $this->hasMany(DataPostmortem::class, 'diperiksa_oleh');
    }

    public function verifikasiKecocokan()
    {
        return $this->hasMany(KecocokanIdentifikasi::class, 'diverifikasi_oleh');
    }
}
