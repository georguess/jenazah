<?php

namespace Tests\Feature;

use App\Models\DataPostmortem;
use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * "Menyembunyikan menu bukan membatasi" (pertemuan 7): setiap peran mengetik
 * URL secara langsung, dan server harus menjawab 200 atau 403 sesuai matriks
 * di docs/rbac.md — tanpa bergantung pada menu atau tombol di React.
 */
class OtorisasiPeranTest extends TestCase
{
    use RefreshDatabase;

    private const PERAN = ['admin', 'petugas_administrasi', 'petugas_forensik', 'keluarga_pelapor'];

    /**
     * rute => [admin, administrasi, forensik, keluarga]. Parameter {jenazah},
     * {laporan}, dst. diisi data milik orang lain (bukan milik keluarga uji).
     */
    public static function matriks(): array
    {
        return [
            'dasbor' => ['dashboard', [200, 200, 200, 200]],
            'jenazah.index' => ['jenazah.index', [200, 200, 200, 403]],
            'jenazah.create' => ['jenazah.create', [200, 200, 403, 403]],
            'jenazah.show' => ['jenazah.show', [200, 200, 200, 403]],
            'jenazah.edit' => ['jenazah.edit', [200, 200, 403, 403]],
            'laporan.index' => ['laporan.index', [200, 200, 403, 200]],
            'laporan.create' => ['laporan.create', [200, 200, 403, 200]],
            'laporan.show (milik orang lain)' => ['laporan.show', [200, 200, 200, 403]],
            'laporan.edit (milik orang lain)' => ['laporan.edit', [200, 200, 403, 403]],
            'antemortem.index' => ['antemortem.index', [403, 403, 403, 200]],
            'antemortem.create' => ['antemortem.create', [200, 200, 403, 200]],
            'postmortem.index' => ['postmortem.index', [403, 403, 200, 403]],
            'postmortem.create' => ['postmortem.create', [200, 403, 200, 403]],
            'postmortem.show' => ['postmortem.show', [200, 403, 200, 403]],
            'kecocokan.index' => ['kecocokan.index', [200, 403, 200, 403]],
            'kecocokan.create' => ['kecocokan.create', [403, 403, 200, 403]],
            'kecocokan.show' => ['kecocokan.show', [200, 403, 200, 403]],
            'dokumen.index' => ['dokumen.index', [403, 200, 403, 403]],
            'dokumen.create' => ['dokumen.create', [200, 200, 403, 403]],
            'pengguna.index' => ['pengguna.index', [200, 403, 403, 403]],
            'pengguna.create' => ['pengguna.create', [200, 403, 403, 403]],
        ];
    }

    #[DataProvider('matriks')]
    public function test_mengetik_url_langsung_mengikuti_matriks_rbac(string $rute, array $harapan): void
    {
        $parameter = $this->parameterUntuk($rute);

        foreach (self::PERAN as $i => $peran) {
            $user = User::factory()->peran($peran)->create();

            $this->actingAs($user)
                ->get(route($rute, $parameter))
                ->assertStatus($harapan[$i]);
        }
    }

    #[DataProvider('matriks')]
    public function test_tamu_diarahkan_ke_halaman_masuk(string $rute, array $harapan): void
    {
        $this->get(route($rute, $this->parameterUntuk($rute)))->assertRedirect(route('login'));
    }

    public function test_aksi_tulis_ditolak_403_walau_dikirim_tanpa_antarmuka(): void
    {
        $forensik = User::factory()->forensik()->create();
        $keluarga = User::factory()->keluarga()->create();
        $jenazah = Jenazah::factory()->create();

        // Petugas forensik tidak boleh menghapus/mengubah jenazah (curl -X DELETE).
        $this->actingAs($forensik)->delete(route('jenazah.destroy', $jenazah))->assertForbidden();
        $this->actingAs($forensik)->put(route('jenazah.update', $jenazah), ['kode_jenazah' => 'X'])->assertForbidden();
        $this->assertModelExists($jenazah);

        // Keluarga tidak boleh membuat postmortem maupun memverifikasi.
        $this->actingAs($keluarga)->post(route('postmortem.store'), ['jenazah_id' => $jenazah->id])->assertForbidden();
        $kecocokan = KecocokanIdentifikasi::factory()->create();
        $this->actingAs($keluarga)->patch(route('kecocokan.verifikasi', $kecocokan), ['status_verifikasi' => 'disetujui'])->assertForbidden();
    }

    public function test_halaman_403_ramah_dirender_lewat_inertia(): void
    {
        $this->actingAs(User::factory()->keluarga()->create())
            ->get(route('jenazah.index'))
            ->assertForbidden()
            ->assertInertia(fn (Assert $page) => $page->component('galat')->where('status', 403)->has('menu'));
    }

    private function parameterUntuk(string $rute): array
    {
        return match (explode('.', $rute)[0]) {
            'jenazah' => str_ends_with($rute, 'index') || str_ends_with($rute, 'create') ? [] : [Jenazah::factory()->create()],
            'laporan' => str_ends_with($rute, 'index') || str_ends_with($rute, 'create') ? [] : [LaporanKehilangan::factory()->create()],
            'postmortem' => str_ends_with($rute, 'show') ? [DataPostmortem::factory()->create()] : [],
            'kecocokan' => str_ends_with($rute, 'show') ? [KecocokanIdentifikasi::factory()->create()] : [],
            default => [],
        };
    }
}
