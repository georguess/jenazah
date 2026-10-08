<?php

namespace Database\Seeders;

use App\Models\DataPostmortem;
use App\Models\Jenazah;
use App\Models\User;
use Illuminate\Database\Seeder;

class DataPostmortemSeeder extends Seeder
{
    public function run(): void
    {
        $forensik = User::where('role', User::ROLE_PETUGAS_FORENSIK)->first();
        $jenazah = Jenazah::where('kode_jenazah', 'JZ-2026-003')->first();

        if ($jenazah && $forensik) {
            DataPostmortem::updateOrCreate(
                ['jenazah_id' => $jenazah->id],
                [
                    'gigi_geligi' => 'Kehilangan gigi geraham bawah kiri',
                    'ciri_khusus' => 'Bekas luka operasi lutut kanan',
                    'golongan_darah' => 'O',
                    'estimasi_tinggi_badan_cm' => 166.0,
                    'estimasi_berat_badan_kg' => 60.0,
                    'kode_sampel_dna' => 'DNA-2026-003',
                    'diperiksa_oleh' => $forensik->id,
                    'tanggal_pemeriksaan' => now()->subDays(2)->toDateString(),
                ]
            );
        }
    }
}
