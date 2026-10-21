<?php

namespace Database\Factories;

use App\Models\DataAntemortem;
use App\Models\LaporanKehilangan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataAntemortem>
 */
class DataAntemortemFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');

        return [
            'laporan_kehilangan_id' => LaporanKehilangan::factory(),
            'gigi_geligi' => $faker->randomElement(['Gigi seri atas kiri patah', 'Tambalan amalgam geraham kanan bawah', 'Lengkap, tidak ada kelainan', 'Memakai gigi palsu atas']),
            'ciri_khusus' => $faker->randomElement(['Tato burung di lengan kanan', 'Bekas operasi usus buntu', 'Tahi lalat besar di leher', null]),
            'golongan_darah' => $faker->randomElement(array_keys(DataAntemortem::GOLONGAN_DARAH)),
            'tinggi_badan_cm' => $faker->randomFloat(1, 140, 185),
            'berat_badan_kg' => $faker->randomFloat(1, 40, 95),
            'riwayat_medis' => $faker->optional()->randomElement(['Patah tulang paha kiri 2018', 'Diabetes, rutin kontrol', 'Pernah operasi lutut']),
        ];
    }
}
