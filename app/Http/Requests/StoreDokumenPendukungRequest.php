<?php

namespace App\Http\Requests;

use App\Models\DokumenPendukung;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDokumenPendukungRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Cermin CHECK num_nonnulls(...) = 1: tepat satu induk, tidak dua, tidak kosong. */
    public function rules(): array
    {
        return [
            'jenazah_id' => ['nullable', 'required_without:laporan_kehilangan_id', 'prohibits:laporan_kehilangan_id', 'integer', Rule::exists('jenazah', 'id')],
            'laporan_kehilangan_id' => ['nullable', 'required_without:jenazah_id', 'integer', Rule::exists('laporan_kehilangan', 'id')],
            'jenis_dokumen' => ['required', Rule::in(array_keys(DokumenPendukung::JENIS))],
            'berkas' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
