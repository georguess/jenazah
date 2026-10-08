<?php

namespace Database\Seeders;

use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaporanKehilanganSeeder extends Seeder
{
    public function run(): void
    {
        $pelapor = User::where('role', User::ROLE_KELUARGA_PELAPOR)->first();

        $data = [
            ['nama_pelapor' => 'Joko Prasetyo', 'no_hp_pelapor' => '081234567890', 'hubungan_dengan_korban' => 'anak kandung', 'nama_korban_hilang' => 'Slamet Prasetyo', 'jenis_kelamin' => 'laki-laki', 'usia_perkiraan' => 60, 'ciri_fisik' => 'Tinggi 165cm, bekas luka operasi di lutut kanan', 'lokasi_terakhir' => 'Desa Sukajaya, Way Lima', 'status' => 'cocok'],
            ['nama_pelapor' => 'Ratna Sari', 'no_hp_pelapor' => '081298765432', 'hubungan_dengan_korban' => 'istri', 'nama_korban_hilang' => 'Ahmad Fauzi', 'jenis_kelamin' => 'laki-laki', 'usia_perkiraan' => 35, 'ciri_fisik' => 'Tato bertuliskan nama anak di lengan kiri', 'lokasi_terakhir' => 'Desa Sukajaya, Way Lima', 'status' => 'aktif'],
        ];

        foreach ($data as $row) {
            LaporanKehilangan::updateOrCreate(
                ['nama_korban_hilang' => $row['nama_korban_hilang']],
                $row + [
                    'tanggal_hilang' => now()->subDays(rand(5, 15))->toDateString(),
                    'dicatat_oleh' => $pelapor?->id,
                ]
            );
        }
    }
}
