<?php

namespace App\Policies;

use App\Models\DokumenPendukung;
use App\Models\User;

class DokumenPendukungPolicy
{
    /** Daftar dokumen khusus petugas administrasi; admin membukanya dari halaman jenazah/laporan. */
    public function viewAny(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_PETUGAS_ADMINISTRASI);
    }

    public function view(User $user, DokumenPendukung $dokumen): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI);
    }

    public function create(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI);
    }

    /** Dokumen hanya dihapus pengunggahnya atau admin. */
    public function delete(User $user, DokumenPendukung $dokumen): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN)
            || ($user->punyaPeran(User::ROLE_PETUGAS_ADMINISTRASI) && $dokumen->diunggah_oleh === $user->id);
    }
}
