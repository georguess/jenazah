<?php

namespace Database\Factories;

use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KecocokanIdentifikasi>
 */
class KecocokanIdentifikasiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'jenazah_id' => Jenazah::factory(),
            'laporan_kehilangan_id' => LaporanKehilangan::factory(),
            'metode_kecocokan' => fake()->randomElement(array_keys(KecocokanIdentifikasi::METODE)),
            'skor_kecocokan' => fake()->randomFloat(2, 40, 99),
            'diajukan_oleh' => User::factory()->forensik(),
        ];
    }
}
