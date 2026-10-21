<?php

namespace Database\Seeders;

use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KecocokanIdentifikasiSeeder extends Seeder
{
    public function run(): void
    {
        $jenazah = Jenazah::where('kode_jenazah', 'JZ-2026-003')->first();
        $laporan = LaporanKehilangan::where('nama_korban_hilang', 'Slamet Prasetyo')->first();
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $forensik = User::where('role', User::ROLE_PETUGAS_FORENSIK)->first();

        if ($jenazah && $laporan && $admin) {
            $kecocokan = KecocokanIdentifikasi::updateOrCreate(
                [
                    'jenazah_id' => $jenazah->id,
                    'laporan_kehilangan_id' => $laporan->id,
                    'metode_kecocokan' => 'gigi',
                ],
                [
                    'skor_kecocokan' => 92.50,
                    'diajukan_oleh' => $forensik?->id,
                ]
            );

            // Kolom verifikasi tidak fillable (hanya lewat verifikasi admin).
            $kecocokan->forceFill([
                'status_verifikasi' => 'disetujui',
                'diverifikasi_oleh' => $admin->id,
                'catatan_verifikasi' => 'Ciri gigi dan bekas luka operasi cocok dengan data antemortem.',
                'tanggal_verifikasi' => now()->subDay(),
            ])->save();
        }
    }
}
