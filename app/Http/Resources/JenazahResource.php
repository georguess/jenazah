<?php

namespace App\Http\Resources;

use App\Models\Jenazah;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Bentuk data jenazah yang dikirim ke React: hanya kolom yang ditampilkan,
 * alamat dari route(), dan kewenangan `can` yang dihitung server (Policy).
 *
 * @mixin Jenazah
 */
class JenazahResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_jenazah' => $this->kode_jenazah,
            'tanggal_ditemukan' => $this->tanggal_ditemukan?->format('Y-m-d'),
            'lokasi_ditemukan' => $this->lokasi_ditemukan,
            'jenis_kelamin' => $this->jenis_kelamin,
            'jenis_kelamin_label' => Jenazah::JENIS_KELAMIN[$this->jenis_kelamin] ?? '—',
            'perkiraan_usia' => $this->perkiraan_usia,
            'kondisi_jenazah' => $this->kondisi_jenazah,
            'kondisi_label' => Jenazah::KONDISI[$this->kondisi_jenazah] ?? $this->kondisi_jenazah,
            'status_identifikasi' => $this->status_identifikasi,
            'status_label' => Jenazah::STATUS_IDENTIFIKASI[$this->status_identifikasi] ?? $this->status_identifikasi,
            'catatan' => $this->catatan,
            'pencatat' => $this->whenLoaded('pencatat', fn () => $this->pencatat->name),
            'url' => [
                'lihat' => route('jenazah.show', $this->resource),
                'ubah' => route('jenazah.edit', $this->resource),
                'hapus' => route('jenazah.destroy', $this->resource),
            ],
            'can' => [
                'ubah' => $request->user()->can('update', $this->resource),
                'hapus' => $request->user()->can('delete', $this->resource),
            ],
        ];
    }
}
