<?php

namespace Database\Factories;

use App\Models\Jenazah;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Data karangan yang wajar: kode berpola lapangan, lokasi bernama panjang
 * (menguji tata letak tabel), tanggal tidak di masa depan.
 *
 * @extends Factory<Jenazah>
 */
class JenazahFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');

        return [
            'kode_jenazah' => 'JZ-'.$faker->unique()->numerify('2026-F###'),
            'tanggal_ditemukan' => $faker->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
            'lokasi_ditemukan' => $faker->randomElement([
                'Bantaran Sungai Way Sekampung, Desa '.$faker->city(),
                'Lokasi longsor Jalan Lintas Barat Sumatra, Kecamatan '.$faker->city(),
                'Instalasi Gawat Darurat RSUD '.$faker->lastName(),
                'Pesisir Pantai '.$faker->city().', Kabupaten Lampung Selatan',
            ]),
            'jenis_kelamin' => $faker->randomElement(array_keys(Jenazah::JENIS_KELAMIN)),
            'perkiraan_usia' => $faker->optional(0.8)->numberBetween(1, 90),
            'kondisi_jenazah' => $faker->randomElement(array_keys(Jenazah::KONDISI)),
            'catatan' => $faker->optional()->sentence(12),
            'dicatat_oleh' => User::factory()->administrasi(),
        ];
    }

    /** Status hanya lewat forceFill (tidak fillable) — sama seperti alur verifikasi. */
    public function status(string $status): static
    {
        return $this->afterMaking(fn (Jenazah $j) => $j->forceFill(['status_identifikasi' => $status]));
    }
}
