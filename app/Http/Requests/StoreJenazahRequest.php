<?php

namespace App\Http\Requests;

use App\Models\Jenazah;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJenazahRequest extends FormRequest
{
    /** Otorisasi dilakukan di controller lewat Gate::authorize + JenazahPolicy. */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_jenazah' => ['required', 'string', 'max:30', Rule::unique('jenazah', 'kode_jenazah')],
            'tanggal_ditemukan' => ['required', 'date', 'before_or_equal:today'],
            'lokasi_ditemukan' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', Rule::in(array_keys(Jenazah::JENIS_KELAMIN))],
            'perkiraan_usia' => ['nullable', 'integer', 'between:0,120'],
            'kondisi_jenazah' => ['required', Rule::in(array_keys(Jenazah::KONDISI))],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
