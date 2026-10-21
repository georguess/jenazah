<?php

namespace App\Http\Resources;

use App\Models\DataAntemortem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DataAntemortem */
class DataAntemortemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'laporan_kehilangan_id' => $this->laporan_kehilangan_id,
            'nama_korban_hilang' => $this->whenLoaded('laporanKehilangan', fn () => $this->laporanKehilangan->nama_korban_hilang),
            'gigi_geligi' => $this->gigi_geligi,
            'ciri_khusus' => $this->ciri_khusus,
            'golongan_darah' => $this->golongan_darah,
            'tinggi_badan_cm' => $this->tinggi_badan_cm,
            'berat_badan_kg' => $this->berat_badan_kg,
            'riwayat_medis' => $this->riwayat_medis,
            'url' => [
                'lihat' => route('antemortem.show', $this->resource),
                'ubah' => route('antemortem.edit', $this->resource),
                'hapus' => route('antemortem.destroy', $this->resource),
                'laporan' => route('laporan.show', $this->laporan_kehilangan_id),
            ],
            'can' => [
                'ubah' => $request->user()->can('update', $this->resource),
                'hapus' => $request->user()->can('delete', $this->resource),
            ],
        ];
    }
}
