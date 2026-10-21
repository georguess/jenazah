<?php

namespace App\Policies;

use App\Models\LaporanKehilangan;
use App\Models\User;

class LaporanKehilanganPolicy
{
    /**
     * Daftar laporan: admin, administrasi, dan keluarga (yang terakhir hanya
     * menerima laporannya sendiri — disaring di kueri lewat scopeTerlihatOleh).
     * Petugas forensik tidak punya daftar; ia membuka laporan tertentu dari
     * halaman kecocokan untuk membandingkan data.
     */
    public function viewAny(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI, User::ROLE_KELUARGA_PELAPOR);
    }

    public function view(User $user, LaporanKehilangan $laporan): bool
    {
        if ($user->punyaPeran(User::ROLE_KELUARGA_PELAPOR)) {
            return $laporan->milik($user);
        }

        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI, User::ROLE_PETUGAS_FORENSIK);
    }

    public function create(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI, User::ROLE_KELUARGA_PELAPOR);
    }

    /** Keluarga hanya mengubah laporannya sendiri dan selama laporan masih aktif. */
    public function update(User $user, LaporanKehilangan $laporan): bool
    {
        if ($user->punyaPeran(User::ROLE_KELUARGA_PELAPOR)) {
            return $laporan->milik($user) && $laporan->status === LaporanKehilangan::STATUS_AKTIF;
        }

        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI);
    }

    /** Mengubah status (aktif/ditutup) hanya petugas; status "cocok" lahir dari verifikasi. */
    public function ubahStatus(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI);
    }

    public function delete(User $user, LaporanKehilangan $laporan): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI);
    }
}
