<?php

namespace App\Policies;

use App\Models\DataPostmortem;
use App\Models\User;

class DataPostmortemPolicy
{
    /** Daftar pemeriksaan milik petugas forensik; admin membukanya dari halaman jenazah. */
    public function viewAny(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_PETUGAS_FORENSIK);
    }

    public function view(User $user, DataPostmortem $postmortem): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_FORENSIK);
    }

    public function create(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_FORENSIK);
    }

    /** Hasil pemeriksaan hanya diubah dokter yang memeriksa (chain of custody) atau admin. */
    public function update(User $user, DataPostmortem $postmortem): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN)
            || ($user->punyaPeran(User::ROLE_PETUGAS_FORENSIK) && $postmortem->diperiksa_oleh === $user->id);
    }

    public function delete(User $user, DataPostmortem $postmortem): bool
    {
        return $this->update($user, $postmortem);
    }
}
