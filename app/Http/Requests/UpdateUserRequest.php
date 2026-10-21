<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Kata sandi boleh dikosongkan bila tidak ingin diganti. */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('pengguna'))],
            'role' => ['required', Rule::in(array_keys(User::DAFTAR_ROLE))],
            'password' => ['nullable', 'string', Password::defaults(), 'confirmed'],
        ];
    }
}
