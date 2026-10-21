<?php

namespace Tests\Feature;

use App\Models\DataPostmortem;
use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** "Uji laptop kosong": migrate:fresh --seed harus berjalan di PostgreSQL. */
class MigrasiDanSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrate_fresh_seed_berjalan(): void
    {
        $this->artisan('migrate:fresh', ['--seed' => true])->assertSuccessful();

        $this->assertSame(5, User::count());
        $this->assertGreaterThanOrEqual(30, Jenazah::count());
        $this->assertSame('teridentifikasi', Jenazah::firstWhere('kode_jenazah', 'JZ-2026-003')->status_identifikasi);
        $this->assertTrue(User::whereNotNull('email_verified_at')->count() === 5, 'Seeder mengisi email_verified_at sehingga middleware verified aman dipakai.');
        $this->assertGreaterThan(0, KecocokanIdentifikasi::where('status_verifikasi', 'menunggu')->count());
        $this->assertGreaterThan(0, DataPostmortem::count());

        // Kembalikan basis data uji ke keadaan kosong untuk kelas uji berikutnya.
        $this->artisan('migrate:fresh')->assertSuccessful();
    }

    public function test_check_role_menolak_nilai_di_luar_daftar(): void
    {
        $this->expectException(QueryException::class);

        DB::table('users')->insert([
            'name' => 'X', 'email' => 'x@contoh.test', 'password' => 'x', 'role' => 'superadmin',
        ]);
    }

    public function test_check_dokumen_menolak_dua_induk(): void
    {
        $this->expectException(QueryException::class);

        $jenazah = Jenazah::factory()->create();
        DB::table('dokumen_pendukung')->insert([
            'jenazah_id' => $jenazah->id,
            'laporan_kehilangan_id' => LaporanKehilangan::factory()->create()->id,
            'jenis_dokumen' => 'foto',
            'path_file' => 'x.jpg',
            'diunggah_oleh' => $jenazah->dicatat_oleh,
        ]);
    }

    public function test_status_bawaan_jenazah_muat_di_kolomnya(): void
    {
        // Cacat migrasi lama: varchar(20) untuk nilai bawaan 21 karakter.
        $jenazah = Jenazah::factory()->create();

        $this->assertSame('belum_teridentifikasi', $jenazah->fresh()->status_identifikasi);
    }
}
