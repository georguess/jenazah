<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Registrasi publik SELALU menghasilkan akun keluarga pelapor.
     * Akun petugas dan admin hanya dibuat oleh admin lewat menu Pengguna.
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = new User([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
        $user->role = User::ROLE_KELUARGA_PELAPOR;
        $user->save();

        return $user;
    }
}
