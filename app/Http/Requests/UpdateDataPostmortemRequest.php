<?php

namespace App\Http\Requests;

class UpdateDataPostmortemRequest extends StoreDataPostmortemRequest
{
    public function rules(): array
    {
        return $this->aturanPemeriksaan();
    }
}
