<?php

namespace Database\Factories;

use App\Models\DataAntemortem;
use App\Models\DataPostmortem;
use App\Models\Jenazah;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataPostmortem>
 */
class DataPostmortemFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');

        return [
            'jenazah_id' => Jenazah::factory(),
            'gigi_geligi' => $faker->randomElement(['Gigi seri atas kiri patah', 'Tambalan amalgam geraham kanan bawah', 'Lengkap']),
            'ciri_khusus' => $faker->optional()->randomElement(['Tato burung di lengan kanan', 'Bekas operasi di perut kanan bawah']),
            'golongan_darah' => $faker->randomElement(array_keys(DataAntemortem::GOLONGAN_DARAH)),
            'estimasi_tinggi_badan_cm' => $faker->randomFloat(1, 140, 185),
            'estimasi_berat_badan_kg' => $faker->randomFloat(1, 40, 95),
            'kode_sampel_dna' => $faker->optional()->bothify('DNA-2026-####'),
            'diperiksa_oleh' => User::factory()->forensik(),
            'tanggal_pemeriksaan' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
        ];
    }
}
