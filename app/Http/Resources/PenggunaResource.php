<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class PenggunaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'label_peran' => $this->labelPeran(),
            'terverifikasi' => $this->email_verified_at !== null,
            'url' => [
                'lihat' => route('pengguna.show', $this->resource),
                'ubah' => route('pengguna.edit', $this->resource),
                'hapus' => route('pengguna.destroy', $this->resource),
            ],
            'can' => [
                'ubah' => $request->user()->can('update', $this->resource),
                'hapus' => $request->user()->can('delete', $this->resource),
            ],
        ];
    }
}
