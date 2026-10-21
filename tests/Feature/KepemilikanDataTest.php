<?php

namespace Tests\Feature;

use App\Models\DataAntemortem;
use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Keluarga pelapor hanya melihat dan mengubah data miliknya — disaring di kueri. */
class KepemilikanDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_daftar_laporan_keluarga_hanya_berisi_laporan_miliknya(): void
    {
        $keluarga = User::factory()->keluarga()->create();
        $milik = LaporanKehilangan::factory()->count(2)->milik($keluarga)->create();
        LaporanKehilangan::factory()->count(3)->create();

        $this->actingAs($keluarga)->get(route('laporan.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('laporan/index')
                ->has('laporan.data', 2)
                ->where('laporan.meta.total', 2)
                ->where('laporan.data.0.id', fn ($id) => $milik->pluck('id')->contains($id)));
    }

    public function test_petugas_administrasi_melihat_semua_laporan(): void
    {
        LaporanKehilangan::factory()->count(3)->create();

        $this->actingAs(User::factory()->administrasi()->create())->get(route('laporan.index'))
            ->assertInertia(fn (Assert $page) => $page->where('laporan.meta.total', 3));
    }

    public function test_keluarga_mengubah_laporannya_sendiri_tetapi_tidak_milik_orang_lain(): void
    {
        $keluarga = User::factory()->keluarga()->create();
        $milik = LaporanKehilangan::factory()->milik($keluarga)->create();
        $orangLain = LaporanKehilangan::factory()->create();
        $isian = LaporanKehilangan::factory()->make(['nama_korban_hilang' => 'Nama Baru'])->only((new LaporanKehilangan)->getFillable());
        unset($isian['status'], $isian['dicatat_oleh']);

        $this->actingAs($keluarga)->put(route('laporan.update', $milik), $isian)->assertRedirect(route('laporan.show', $milik));
        $this->assertSame('Nama Baru', $milik->fresh()->nama_korban_hilang);

        $this->actingAs($keluarga)->put(route('laporan.update', $orangLain), $isian)->assertForbidden();
        $this->actingAs($keluarga)->get(route('laporan.show', $orangLain))->assertForbidden();
    }

    public function test_keluarga_tidak_boleh_mengubah_status_laporan(): void
    {
        $keluarga = User::factory()->keluarga()->create();
        $milik = LaporanKehilangan::factory()->milik($keluarga)->create();
        $isian = $milik->only(['nama_pelapor', 'no_hp_pelapor', 'hubungan_dengan_korban', 'nama_korban_hilang', 'lokasi_terakhir'])
            + ['tanggal_hilang' => $milik->tanggal_hilang->format('Y-m-d'), 'status' => 'ditutup'];

        $this->actingAs($keluarga)->from(route('laporan.edit', $milik))
            ->put(route('laporan.update', $milik), $isian)
            ->assertSessionHasErrors('status');
        $this->assertSame('aktif', $milik->fresh()->status);
    }

    public function test_laporan_yang_sudah_cocok_terkunci_bagi_keluarga(): void
    {
        $keluarga = User::factory()->keluarga()->create();
        $laporan = LaporanKehilangan::factory()->milik($keluarga)->create(['status' => LaporanKehilangan::STATUS_COCOK]);

        $this->actingAs($keluarga)->get(route('laporan.edit', $laporan))->assertForbidden();
    }

    public function test_daftar_antemortem_saya_disaring_di_server(): void
    {
        $keluarga = User::factory()->keluarga()->create();
        DataAntemortem::factory()->for(LaporanKehilangan::factory()->milik($keluarga))->create();
        DataAntemortem::factory()->count(2)->create();

        $this->actingAs($keluarga)->get(route('antemortem.index'))
            ->assertInertia(fn (Assert $page) => $page->has('antemortem.data', 1));
    }

    public function test_keluarga_tidak_bisa_menautkan_antemortem_ke_laporan_orang_lain(): void
    {
        $keluarga = User::factory()->keluarga()->create();
        $orangLain = LaporanKehilangan::factory()->create();

        $this->actingAs($keluarga)->from(route('antemortem.create'))
            ->post(route('antemortem.store'), ['laporan_kehilangan_id' => $orangLain->id, 'golongan_darah' => 'O'])
            ->assertSessionHasErrors('laporan_kehilangan_id');
        $this->assertDatabaseCount('data_antemortem', 0);
    }

    public function test_dasbor_keluarga_tidak_menerima_statistik(): void
    {
        $this->actingAs(User::factory()->keluarga()->create())->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page->where('statistik', null));

        $this->actingAs(User::factory()->admin()->create())->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page->has('statistik', 4));
    }
}
