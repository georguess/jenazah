<?php

namespace App\Http\Resources;

use App\Models\Jenazah;
use App\Models\LaporanKehilangan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LaporanKehilangan */
class LaporanKehilanganResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_pelapor' => $this->nama_pelapor,
            'no_hp_pelapor' => $this->no_hp_pelapor,
            'hubungan_dengan_korban' => $this->hubungan_dengan_korban,
            'nama_korban_hilang' => $this->nama_korban_hilang,
            'jenis_kelamin' => $this->jenis_kelamin,
            'jenis_kelamin_label' => Jenazah::JENIS_KELAMIN[$this->jenis_kelamin] ?? '—',
            'usia_perkiraan' => $this->usia_perkiraan,
            'ciri_fisik' => $this->ciri_fisik,
            'tanggal_hilang' => $this->tanggal_hilang?->format('Y-m-d'),
            'lokasi_terakhir' => $this->lokasi_terakhir,
            'status' => $this->status,
            'status_label' => LaporanKehilangan::STATUS[$this->status] ?? $this->status,
            'pencatat' => $this->whenLoaded('pencatat', fn () => $this->pencatat?->name),
            'url' => [
                'lihat' => route('laporan.show', $this->resource),
                'ubah' => route('laporan.edit', $this->resource),
                'hapus' => route('laporan.destroy', $this->resource),
            ],
            'can' => [
                'ubah' => $request->user()->can('update', $this->resource),
                'hapus' => $request->user()->can('delete', $this->resource),
            ],
        ];
    }
}
