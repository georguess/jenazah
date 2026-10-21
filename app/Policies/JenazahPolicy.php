<?php

namespace App\Policies;

use App\Models\Jenazah;
use App\Models\User;

class JenazahPolicy
{
    /** Keluarga pelapor tidak boleh melihat data jenazah sama sekali. */
    public function viewAny(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI, User::ROLE_PETUGAS_FORENSIK);
    }

    public function view(User $user, Jenazah $jenazah): bool
    {
        return $this->viewAny($user);
    }

    /** Petugas forensik hanya melihat; pencatatan jenazah tugas administrasi. */
    public function create(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI);
    }

    public function update(User $user, Jenazah $jenazah): bool
    {
        return $this->create($user);
    }

    /**
     * Menghapus jenazah ikut menghapus (cascade) postmortem dan kecocokan.
     * Petugas administrasi hanya boleh menghapus jenazah yang belum diperiksa
     * forensik (mis. salah input); admin boleh semuanya.
     */
    public function delete(User $user, Jenazah $jenazah): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN)
            || ($user->punyaPeran(User::ROLE_PETUGAS_ADMINISTRASI) && ! $jenazah->sudahDiperiksa());
    }
}
