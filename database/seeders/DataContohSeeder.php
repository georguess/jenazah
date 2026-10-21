<?php

namespace Database\Seeders;

use App\Models\DataAntemortem;
use App\Models\DataPostmortem;
use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Data karangan dalam jumlah wajar (lewat factory) agar halaman daftar,
 * paginasi, dan tata letak teruji — termasuk nama sepanjang ±60 huruf.
 * Memakai akun yang sudah dibuat UserSeeder, tidak membuat akun baru.
 */
class DataContohSeeder extends Seeder
{
    public function run(): void
    {
        $administrasi = User::where('role', User::ROLE_PETUGAS_ADMINISTRASI)->firstOrFail();
        $forensik = User::where('role', User::ROLE_PETUGAS_FORENSIK)->firstOrFail();
        $pelapor = User::where('role', User::ROLE_KELUARGA_PELAPOR)->firstOrFail();

        $jenazah = Jenazah::factory()->count(30)->create(['dicatat_oleh' => $administrasi->id]);

        $laporan = LaporanKehilangan::factory()->count(20)->create(['dicatat_oleh' => $administrasi->id])
            ->merge(LaporanKehilangan::factory()->count(3)->milik($pelapor)->create())
            ->push(LaporanKehilangan::factory()->namaPanjang()->milik($pelapor)->create());

        $laporan->take(12)->each(fn (LaporanKehilangan $l) => DataAntemortem::factory()->for($l)->create());

        $jenazah->take(10)->each(fn (Jenazah $j) => DataPostmortem::factory()->for($j)->create(['diperiksa_oleh' => $forensik->id]));

        $jenazah->take(6)->each(fn (Jenazah $j, int $i) => KecocokanIdentifikasi::ajukan([
            'jenazah_id' => $j->id,
            'laporan_kehilangan_id' => $laporan[$i]->id,
            'metode_kecocokan' => array_keys(KecocokanIdentifikasi::METODE)[$i % 5],
            'skor_kecocokan' => 60 + $i * 5,
        ], $forensik));
    }
}
