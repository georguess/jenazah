<?php

namespace App\Http\Requests;

use App\Models\KecocokanIdentifikasi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifikasiKecocokanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_verifikasi' => ['required', Rule::in([KecocokanIdentifikasi::STATUS_DISETUJUI, KecocokanIdentifikasi::STATUS_DITOLAK])],
            // Penolakan wajib beralasan agar petugas forensik tahu apa yang kurang.
            'catatan_verifikasi' => ['nullable', 'required_if:status_verifikasi,'.KecocokanIdentifikasi::STATUS_DITOLAK, 'string', 'max:2000'],
        ];
    }
}
