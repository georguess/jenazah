<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return Gate::forUser($user)->allows('kelola-pengguna');
    }

    public function view(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Admin menghapus akun lain (bukan dirinya sendiri, agar sistem tidak
     * kehilangan admin terakhir). Pengguna biasa hanya boleh menghapus akunnya
     * sendiri bila ia keluarga pelapor. Akun yang punya jejak audit tidak dapat
     * dihapus siapa pun (dicek juga oleh FK RESTRICT di basis data).
     */
    public function delete(User $user, User $model): bool
    {
        if ($model->punyaJejakAudit()) {
            return false;
        }

        if ($user->is($model)) {
            return $user->punyaPeran(User::ROLE_KELUARGA_PELAPOR);
        }

        return $this->viewAny($user);
    }
}
