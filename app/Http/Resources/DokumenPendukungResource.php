<?php

namespace App\Http\Resources;

use App\Models\DokumenPendukung;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DokumenPendukung */
class DokumenPendukungResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'jenis_dokumen' => $this->jenis_dokumen,
            'jenis_label' => DokumenPendukung::JENIS[$this->jenis_dokumen] ?? $this->jenis_dokumen,
            'induk' => $this->jenazah_id
                ? 'Jenazah '.($this->jenazah?->kode_jenazah ?? '#'.$this->jenazah_id)
                : 'Laporan '.($this->laporanKehilangan?->nama_korban_hilang ?? '#'.$this->laporan_kehilangan_id),
            'pengunggah' => $this->whenLoaded('pengunggah', fn () => $this->pengunggah->name),
            'diunggah_pada' => $this->created_at?->format('Y-m-d H:i'),
            'url' => [
                'lihat' => route('dokumen.show', $this->resource),
                'unduh' => route('dokumen.unduh', $this->resource),
                'hapus' => route('dokumen.destroy', $this->resource),
                'induk' => $this->jenazah_id
                    ? route('jenazah.show', $this->jenazah_id)
                    : route('laporan.show', $this->laporan_kehilangan_id),
            ],
            'can' => [
                'hapus' => $request->user()->can('delete', $this->resource),
            ],
        ];
    }
}
