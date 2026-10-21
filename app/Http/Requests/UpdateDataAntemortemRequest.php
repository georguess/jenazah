<?php

namespace App\Http\Requests;

class UpdateDataAntemortemRequest extends StoreDataAntemortemRequest
{
    /** Laporan induk tidak dapat dipindah; hanya ciri yang diperbarui. */
    public function rules(): array
    {
        return $this->aturanCiri();
    }
}
