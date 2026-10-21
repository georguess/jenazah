<?php

namespace App\Http\Resources;

use App\Models\KecocokanIdentifikasi;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin KecocokanIdentifikasi */
class KecocokanIdentifikasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'jenazah_id' => $this->jenazah_id,
            'laporan_kehilangan_id' => $this->laporan_kehilangan_id,
            'kode_jenazah' => $this->whenLoaded('jenazah', fn () => $this->jenazah->kode_jenazah),
            'nama_korban_hilang' => $this->whenLoaded('laporanKehilangan', fn () => $this->laporanKehilangan->nama_korban_hilang),
            'metode_kecocokan' => $this->metode_kecocokan,
            'metode_label' => KecocokanIdentifikasi::METODE[$this->metode_kecocokan] ?? $this->metode_kecocokan,
            'skor_kecocokan' => $this->skor_kecocokan,
            'status_verifikasi' => $this->status_verifikasi,
            'status_label' => KecocokanIdentifikasi::STATUS_VERIFIKASI[$this->status_verifikasi] ?? $this->status_verifikasi,
            'catatan_verifikasi' => $this->catatan_verifikasi,
            'tanggal_verifikasi' => $this->tanggal_verifikasi?->format('Y-m-d H:i'),
            'pengaju' => $this->whenLoaded('pengaju', fn () => $this->pengaju?->name),
            'verifikator' => $this->whenLoaded('verifikator', fn () => $this->verifikator?->name),
            'url' => [
                'lihat' => route('kecocokan.show', $this->resource),
                'ubah' => route('kecocokan.edit', $this->resource),
                'hapus' => route('kecocokan.destroy', $this->resource),
                'verifikasi' => route('kecocokan.verifikasi', $this->resource),
                'jenazah' => route('jenazah.show', $this->jenazah_id),
                'laporan' => route('laporan.show', $this->laporan_kehilangan_id),
            ],
            'can' => [
                'ubah' => $request->user()->can('update', $this->resource),
                'hapus' => $request->user()->can('delete', $this->resource),
                'verifikasi' => $request->user()->can('verifikasi', $this->resource),
            ],
        ];
    }
}
