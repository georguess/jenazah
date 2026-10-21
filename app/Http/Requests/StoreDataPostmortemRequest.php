<?php

namespace App\Http\Requests;

use App\Models\DataAntemortem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDataPostmortemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenazah_id' => ['required', 'integer', Rule::exists('jenazah', 'id'), Rule::unique('data_postmortem', 'jenazah_id')],
            ...$this->aturanPemeriksaan(),
        ];
    }

    protected function aturanPemeriksaan(): array
    {
        return [
            'gigi_geligi' => ['nullable', 'string', 'max:2000'],
            'ciri_khusus' => ['nullable', 'string', 'max:2000'],
            'golongan_darah' => ['nullable', Rule::in(array_keys(DataAntemortem::GOLONGAN_DARAH))],
            'estimasi_tinggi_badan_cm' => ['nullable', 'numeric', 'decimal:0,1', 'between:30,250'],
            'estimasi_berat_badan_kg' => ['nullable', 'numeric', 'decimal:0,1', 'between:1,300'],
            'kode_sampel_dna' => ['nullable', 'string', 'max:50'],
            'tanggal_pemeriksaan' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
