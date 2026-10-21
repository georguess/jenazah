<?php

namespace App\Http\Requests;

use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKecocokanIdentifikasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenazah_id' => ['required', 'integer', Rule::exists('jenazah', 'id')],
            'laporan_kehilangan_id' => ['required', 'integer',
                Rule::exists('laporan_kehilangan', 'id')->where('status', LaporanKehilangan::STATUS_AKTIF)],
            'metode_kecocokan' => ['required', Rule::in(array_keys(KecocokanIdentifikasi::METODE)),
                // Sama dengan UNIQUE(jenazah_id, laporan_kehilangan_id, metode_kecocokan) di basis data.
                Rule::unique('kecocokan_identifikasi')
                    ->where('jenazah_id', $this->input('jenazah_id'))
                    ->where('laporan_kehilangan_id', $this->input('laporan_kehilangan_id'))],
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
