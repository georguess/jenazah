<?php

namespace App\Http\Resources;

use App\Models\DataPostmortem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DataPostmortem */
class DataPostmortemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'jenazah_id' => $this->jenazah_id,
            'kode_jenazah' => $this->whenLoaded('jenazah', fn () => $this->jenazah->kode_jenazah),
            'gigi_geligi' => $this->gigi_geligi,
            'ciri_khusus' => $this->ciri_khusus,
            'golongan_darah' => $this->golongan_darah,
            'estimasi_tinggi_badan_cm' => $this->estimasi_tinggi_badan_cm,
            'estimasi_berat_badan_kg' => $this->estimasi_berat_badan_kg,
            'kode_sampel_dna' => $this->kode_sampel_dna,
            'tanggal_pemeriksaan' => $this->tanggal_pemeriksaan?->format('Y-m-d'),
            'pemeriksa' => $this->whenLoaded('pemeriksa', fn () => $this->pemeriksa->name),
            'url' => [
                'lihat' => route('postmortem.show', $this->resource),
                'ubah' => route('postmortem.edit', $this->resource),
                'hapus' => route('postmortem.destroy', $this->resource),
                'jenazah' => route('jenazah.show', $this->jenazah_id),
            ],
            'can' => [
                'ubah' => $request->user()->can('update', $this->resource),
                'hapus' => $request->user()->can('delete', $this->resource),
            ],
        ];
    }
}
