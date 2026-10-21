<?php

namespace App\Policies;

use App\Models\DataAntemortem;
use App\Models\User;

/**
 * Antemortem tidak punya kolom pencatat; kepemilikan diturunkan dari
 * laporan_kehilangan.dicatat_oleh, jadi aturannya mengikuti LaporanKehilanganPolicy.
 */
class DataAntemortemPolicy
{
    public function __construct(private LaporanKehilanganPolicy $laporan) {}

    /** Daftar "Data Antemortem Saya" khusus keluarga; staf membukanya dari halaman laporan. */
    public function viewAny(User $user): bool
    {
        return $user->punyaPeran(User::ROLE_KELUARGA_PELAPOR);
    }

    public function view(User $user, DataAntemortem $antemortem): bool
    {
        return $this->laporan->view($user, $antemortem->laporanKehilangan);
    }

    public function create(User $user): bool
    {
        return $this->laporan->create($user);
    }

    public function update(User $user, DataAntemortem $antemortem): bool
    {
        return $this->laporan->update($user, $antemortem->laporanKehilangan);
    }

    public function delete(User $user, DataAntemortem $antemortem): bool
    {
        return $user->punyaPeran(User::ROLE_ADMIN, User::ROLE_PETUGAS_ADMINISTRASI);
    }
}
