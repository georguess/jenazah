<?php

namespace App\Http\Requests;

use App\Models\Jenazah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLaporanKehilanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_pelapor' => ['required', 'string', 'max:255'],
            'no_hp_pelapor' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9]{8,15}$/'],
            'hubungan_dengan_korban' => ['required', 'string', 'max:50'],
            'nama_korban_hilang' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', Rule::in(array_keys(Jenazah::JENIS_KELAMIN))],
            'usia_perkiraan' => ['nullable', 'integer', 'between:0,120'],
            'ciri_fisik' => ['nullable', 'string', 'max:2000'],
            'tanggal_hilang' => ['required', 'date', 'before_or_equal:today'],
            'lokasi_terakhir' => ['required', 'string', 'max:255'],
        ];
    }
}
