<?php

namespace Database\Factories;

use App\Models\DokumenPendukung;
use App\Models\Jenazah;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Bawaan menaut ke jenazah saja (CHECK: tepat satu induk).
 *
 * @extends Factory<DokumenPendukung>
 */
class DokumenPendukungFactory extends Factory
{
    public function definition(): array
    {
        return [
            'jenazah_id' => Jenazah::factory(),
            'laporan_kehilangan_id' => null,
            'jenis_dokumen' => fake()->randomElement(array_keys(DokumenPendukung::JENIS)),
            'path_file' => 'dokumen-pendukung/contoh-'.fake()->uuid().'.pdf',
            'diunggah_oleh' => User::factory()->administrasi(),
        ];
    }
}
