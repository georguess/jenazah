<?php

namespace Tests\Feature;

use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Menu bilah samping dan props `can` dihitung server dari Policy/Gate yang sama. */
class MenuDanKewenanganTest extends TestCase
{
    use RefreshDatabase;

    public static function menuPerPeran(): array
    {
        return [
            'admin' => ['admin', ['Dasbor', 'Jenazah', 'Laporan Kehilangan', 'Kecocokan', 'Pengguna']],
            'petugas_forensik' => ['petugas_forensik', ['Dasbor', 'Jenazah', 'Postmortem', 'Kecocokan']],
            'petugas_administrasi' => ['petugas_administrasi', ['Dasbor', 'Jenazah', 'Laporan', 'Dokumen']],
            'keluarga_pelapor' => ['keluarga_pelapor', ['Dasbor', 'Laporan Saya', 'Data Antemortem Saya']],
        ];
    }

    #[DataProvider('menuPerPeran')]
    public function test_menu_sesuai_peran(string $peran, array $judul): void
    {
        $this->actingAs(User::factory()->peran($peran)->create())
            ->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('menu', fn ($menu) => collect($menu)->pluck('judul')->all() === $judul));
    }

    public function test_props_auth_tidak_memuat_kolom_role(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page->missing('auth.user.role')->has('auth.user.label_peran'));
    }

    public function test_tombol_aksi_jenazah_mengikuti_policy(): void
    {
        $jenazah = Jenazah::factory()->create();

        $this->actingAs(User::factory()->forensik()->create())->get(route('jenazah.show', $jenazah))
            ->assertInertia(fn (Assert $page) => $page
                ->where('jenazah.can.ubah', false)
                ->where('jenazah.can.hapus', false)
                ->where('can.tambahPostmortem', true)
                ->where('dokumen', null));

        $this->actingAs(User::factory()->administrasi()->create())->get(route('jenazah.show', $jenazah))
            ->assertInertia(fn (Assert $page) => $page
                ->where('jenazah.can.ubah', true)
                ->where('jenazah.can.hapus', true)
                ->where('can.tambahPostmortem', false)
                ->where('kecocokan', null));
    }

    public function test_tombol_verifikasi_hanya_untuk_admin(): void
    {
        $kecocokan = KecocokanIdentifikasi::factory()->create();

        $this->actingAs(User::factory()->admin()->create())->get(route('kecocokan.show', $kecocokan))
            ->assertInertia(fn (Assert $page) => $page->where('kecocokan.can.verifikasi', true));

        $this->actingAs($kecocokan->pengaju)->get(route('kecocokan.show', $kecocokan))
            ->assertInertia(fn (Assert $page) => $page
                ->where('kecocokan.can.verifikasi', false)
                ->where('kecocokan.can.ubah', true));
    }

    public function test_daftar_dengan_banyak_baris_tidak_n_plus_1(): void
    {
        // preventLazyLoading aktif saat pengujian: relasi yang lupa di-with() akan galat.
        KecocokanIdentifikasi::factory()->count(12)->create();
        $admin = User::factory()->admin()->create();

        foreach (['jenazah.index', 'kecocokan.index', 'laporan.index', 'pengguna.index'] as $rute) {
            $this->actingAs($admin)->get(route($rute))->assertOk();
        }
    }
}
