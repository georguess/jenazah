<?php

namespace App\Http\Requests;

use App\Models\LaporanKehilangan;
use Illuminate\Validation\Rule;

class UpdateLaporanKehilanganRequest extends StoreLaporanKehilanganRequest
{
    /**
     * Status hanya boleh diubah petugas (aktif/ditutup). Status "cocok"
     * hanya lahir dari verifikasi kecocokan oleh admin.
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'status' => $this->user()->can('ubahStatus', LaporanKehilangan::class)
                ? ['sometimes', Rule::in([LaporanKehilangan::STATUS_AKTIF, 'ditutup'])]
                : ['prohibited'],
        ];
    }
}
