<?php

namespace App\Http\Requests;

use App\Models\DataAntemortem;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDataAntemortemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Keluarga hanya boleh menautkan antemortem ke laporan miliknya sendiri.
        $laporan = Rule::exists('laporan_kehilangan', 'id');
        if ($this->user()->punyaPeran(User::ROLE_KELUARGA_PELAPOR)) {
            $laporan->where('dicatat_oleh', $this->user()->id);
        }

        return [
            'laporan_kehilangan_id' => ['required', 'integer', $laporan, Rule::unique('data_antemortem', 'laporan_kehilangan_id')],
            ...$this->aturanCiri(),
        ];
    }

    protected function aturanCiri(): array
    {
        return [
            'gigi_geligi' => ['nullable', 'string', 'max:2000'],
            'ciri_khusus' => ['nullable', 'string', 'max:2000'],
            'golongan_darah' => ['nullable', Rule::in(array_keys(DataAntemortem::GOLONGAN_DARAH))],
            'tinggi_badan_cm' => ['nullable', 'numeric', 'decimal:0,1', 'between:30,250'],
            'berat_badan_kg' => ['nullable', 'numeric', 'decimal:0,1', 'between:1,300'],
            'riwayat_medis' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
