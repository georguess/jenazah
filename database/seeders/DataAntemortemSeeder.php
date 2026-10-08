<?php

namespace Database\Seeders;

use App\Models\DataAntemortem;
use App\Models\LaporanKehilangan;
use Illuminate\Database\Seeder;

class DataAntemortemSeeder extends Seeder
{
    public function run(): void
    {
        $laporan = LaporanKehilangan::where('nama_korban_hilang', 'Slamet Prasetyo')->first();

        if ($laporan) {
            DataAntemortem::updateOrCreate(
                ['laporan_kehilangan_id' => $laporan->id],
                [
                    'gigi_geligi' => 'Kehilangan gigi geraham bawah kiri',
                    'ciri_khusus' => 'Bekas luka operasi lutut kanan',
                    'golongan_darah' => 'O',
                    'tinggi_badan_cm' => 165.0,
                    'berat_badan_kg' => 62.5,
                    'riwayat_medis' => 'Operasi lutut kanan tahun 2019',
                ]
            );
        }
    }
}
