<?php

namespace Database\Seeders;

use App\Models\Jenazah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class JenazahSeeder extends Seeder
{
    public function run(): void
    {
        $petugas = User::where('role', User::ROLE_PETUGAS_ADMINISTRASI)->first()
            ?? User::where('role', User::ROLE_ADMIN)->firstOrFail();

        $data = [
            ['kode_jenazah' => 'JZ-2026-001', 'lokasi_ditemukan' => 'Lokasi bencana longsor, Kec. Way Lima', 'jenis_kelamin' => 'laki-laki', 'perkiraan_usia' => 34, 'kondisi_jenazah' => 'utuh', 'status_identifikasi' => 'proses'],
            ['kode_jenazah' => 'JZ-2026-002', 'lokasi_ditemukan' => 'Lokasi bencana longsor, Kec. Way Lima', 'jenis_kelamin' => 'perempuan', 'perkiraan_usia' => 27, 'kondisi_jenazah' => 'sebagian', 'status_identifikasi' => 'belum_teridentifikasi'],
            ['kode_jenazah' => 'JZ-2026-003', 'lokasi_ditemukan' => 'RSUD Abdul Moeloek', 'jenis_kelamin' => 'laki-laki', 'perkiraan_usia' => 61, 'kondisi_jenazah' => 'utuh', 'status_identifikasi' => 'teridentifikasi'],
        ];

        foreach ($data as $row) {
            $jenazah = Jenazah::updateOrCreate(
                ['kode_jenazah' => $row['kode_jenazah']],
                Arr::except($row, 'status_identifikasi') + [
                    'tanggal_ditemukan' => now()->subDays(rand(1, 10))->toDateString(),
                    'catatan' => 'Data contoh untuk keperluan demo/seeding.',
                    'dicatat_oleh' => $petugas->id,
                ]
            );

            // status_identifikasi tidak fillable (hanya berubah lewat alur
            // kecocokan), jadi seeder mengisinya secara eksplisit.
            $jenazah->forceFill(['status_identifikasi' => $row['status_identifikasi']])->save();
        }
    }
}
