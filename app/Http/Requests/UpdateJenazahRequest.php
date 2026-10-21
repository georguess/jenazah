<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateJenazahRequest extends StoreJenazahRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'kode_jenazah' => ['required', 'string', 'max:30', Rule::unique('jenazah', 'kode_jenazah')->ignore($this->route('jenazah'))],
        ];
    }
}
