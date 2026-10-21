<?php

namespace App\Http\Requests;

use App\Models\KecocokanIdentifikasi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKecocokanIdentifikasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** Pasangan jenazah–laporan tetap; yang diperbarui metode dan skornya. */
    public function rules(): array
    {
        $kecocokan = $this->route('kecocokan');

        return [
            'metode_kecocokan' => ['required', Rule::in(array_keys(KecocokanIdentifikasi::METODE)),
                Rule::unique('kecocokan_identifikasi')
                    ->where('jenazah_id', $kecocokan->jenazah_id)
                    ->where('laporan_kehilangan_id', $kecocokan->laporan_kehilangan_id)
                    ->ignore($kecocokan)],
            'skor_kecocokan' => ['required', 'numeric', 'decimal:0,2', 'between:0,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'metode_kecocokan.unique' => 'Pasangan jenazah dan laporan ini sudah diajukan dengan metode yang sama.',
        ];
    }
}
