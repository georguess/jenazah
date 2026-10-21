<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake('id_ID')->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => User::ROLE_KELUARGA_PELAPOR,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function peran(string $role): static
    {
        return $this->state(fn () => ['role' => $role]);
    }

    public function admin(): static
    {
        return $this->peran(User::ROLE_ADMIN);
    }

    public function forensik(): static
    {
        return $this->peran(User::ROLE_PETUGAS_FORENSIK);
    }

    public function administrasi(): static
    {
        return $this->peran(User::ROLE_PETUGAS_ADMINISTRASI);
    }

    public function keluarga(): static
    {
        return $this->peran(User::ROLE_KELUARGA_PELAPOR);
    }
}
