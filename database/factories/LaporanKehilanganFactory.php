<?php

namespace Database\Factories;

use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LaporanKehilangan>
 */
class LaporanKehilanganFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('id_ID');

        return [
            'nama_pelapor' => $faker->name(),
            'no_hp_pelapor' => '08'.$faker->numerify('##########'),
            'hubungan_dengan_korban' => $faker->randomElement(['anak kandung', 'istri', 'suami', 'orang tua', 'saudara kandung', 'tetangga']),
            'nama_korban_hilang' => $faker->name(),
            'jenis_kelamin' => $faker->randomElement(['laki-laki', 'perempuan']),
            'usia_perkiraan' => $faker->numberBetween(3, 85),
            'ciri_fisik' => 'Tinggi sekitar '.$faker->numberBetween(140, 185).' cm, '.$faker->randomElement(['rambut ikal', 'berkacamata', 'tahi lalat di pipi kiri', 'bekas luka di dahi']),
            'tanggal_hilang' => $faker->dateTimeBetween('-90 days', '-1 day')->format('Y-m-d'),
            'lokasi_terakhir' => 'Desa '.$faker->city().', Kecamatan '.$faker->city(),
            'status' => LaporanKehilangan::STATUS_AKTIF,
            'dicatat_oleh' => User::factory()->keluarga(),
        ];
    }

    /** Nama sepanjang ±60 huruf untuk menyingkap cacat tata letak (pertemuan 6). */
    public function namaPanjang(): static
    {
        return $this->state(fn () => [
            'nama_korban_hilang' => 'Raden Mas Muhammad Fadhlurrahman Wicaksono Adiningrat Putra',
            'nama_pelapor' => 'Siti Nurhaliza Kusumawardhani Prameswari Hadiningrat Utami',
        ]);
    }

    public function milik(User $user): static
    {
        return $this->state(fn () => ['dicatat_oleh' => $user->id, 'nama_pelapor' => $user->name]);
    }
}
