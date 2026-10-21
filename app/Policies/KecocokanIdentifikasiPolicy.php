<?php

namespace App\Policies;

use App\Models\KecocokanIdentifikasi;
use App\Models\User;

class KecocokanIdentifikasiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_FORENSIK);
    }

    public function view(User $user, KecocokanIdentifikasi $kecocokan): bool
    {
        return $this->viewAny($user);
    }

    /** Mengajukan usulan kecocokan adalah tugas petugas forensik. */
    public function create(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_PETUGAS_FORENSIK);
    }

    /** Usulan hanya boleh diubah pengajunya, dan hanya selama belum diverifikasi. */
    public function update(User $user, KecocokanIdentifikasi $kecocokan): bool
    {
        return $kecocokan->masihMenunggu()
            && $user->punyaPeran(User::ROLE_PETUGAS_FORENSIK)
            && $kecocokan->diajukan_oleh === $user->id;
    }

    public function delete(User $user, KecocokanIdentifikasi $kecocokan): bool
    {
        return $kecocokan->masihMenunggu()
            && ($user->punyaPeran(User::ROLE_ADMIN) || $this->update($user, $kecocokan));
    }

    /** Verifikasi akhir hanya oleh admin. */
    public function verifikasi(User $user, KecocokanIdentifikasi $kecocokan): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN) && $kecocokan->masihMenunggu();
    }
}
