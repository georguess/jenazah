<?php

namespace Tests\Feature;

use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Form Request: galat per isian kembali ke formulir beserta isian lama. */
class ValidasiFormulirTest extends TestCase
{
    use RefreshDatabase;

    public function test_galat_dan_isian_lama_kembali_ke_formulir(): void
    {
        $petugas = User::factory()->administrasi()->create();

        $this->actingAs($petugas)
            ->from(route('jenazah.create'))
            ->post(route('jenazah.store'), [
                'kode_jenazah' => '',
                'tanggal_ditemukan' => now()->addDay()->toDateString(),
                'lokasi_ditemukan' => 'Bantaran Sungai Way Sekampung',
                'kondisi_jenazah' => 'meleleh',
                'perkiraan_usia' => 200,
            ])
            ->assertRedirect(route('jenazah.create'))
            ->assertSessionHasErrors(['kode_jenazah', 'tanggal_ditemukan', 'kondisi_jenazah', 'perkiraan_usia'])
            ->assertSessionDoesntHaveErrors('lokasi_ditemukan')
            ->assertSessionHasInput('lokasi_ditemukan', 'Bantaran Sungai Way Sekampung');

        $this->assertDatabaseCount('jenazah', 0);
    }

    public function test_pesan_galat_berbahasa_indonesia(): void
    {
        $this->actingAs(User::factory()->administrasi()->create())
            ->from(route('jenazah.create'))
            ->post(route('jenazah.store'), [])
            ->assertSessionHasErrors(['kode_jenazah' => 'Kode jenazah wajib diisi.']);
    }

    public function test_kode_jenazah_harus_unik(): void
    {
        $lama = Jenazah::factory()->create();

        $this->actingAs(User::factory()->administrasi()->create())
            ->from(route('jenazah.create'))
            ->post(route('jenazah.store'), [
                'kode_jenazah' => $lama->kode_jenazah,
                'tanggal_ditemukan' => now()->toDateString(),
                'lokasi_ditemukan' => 'RSUD Abdul Moeloek',
                'kondisi_jenazah' => 'utuh',
            ])
            ->assertSessionHasErrors('kode_jenazah');
    }

    public function test_ubah_jenazah_boleh_memakai_kodenya_sendiri(): void
    {
        $jenazah = Jenazah::factory()->create();

        $this->actingAs(User::factory()->administrasi()->create())
            ->put(route('jenazah.update', $jenazah), [
                'kode_jenazah' => $jenazah->kode_jenazah,
                'tanggal_ditemukan' => $jenazah->tanggal_ditemukan->format('Y-m-d'),
                'lokasi_ditemukan' => 'Lokasi diperbarui',
                'kondisi_jenazah' => 'utuh',
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_kecocokan_harus_merujuk_data_yang_ada(): void
    {
        $this->actingAs(User::factory()->forensik()->create())
            ->from(route('kecocokan.create'))
            ->post(route('kecocokan.store'), [
                'jenazah_id' => 99999,
                'laporan_kehilangan_id' => 99999,
                'metode_kecocokan' => 'firasat',
                'skor_kecocokan' => 150,
            ])
            ->assertSessionHasErrors(['jenazah_id', 'laporan_kehilangan_id', 'metode_kecocokan', 'skor_kecocokan']);
    }

    public function test_pasangan_dan_metode_kecocokan_tidak_boleh_ganda(): void
    {
        $lama = KecocokanIdentifikasi::factory()->create();

        $this->actingAs(User::factory()->forensik()->create())
            ->from(route('kecocokan.create'))
            ->post(route('kecocokan.store'), $lama->only(['jenazah_id', 'laporan_kehilangan_id', 'metode_kecocokan']) + ['skor_kecocokan' => 80])
            ->assertSessionHasErrors('metode_kecocokan');
    }

    public function test_dokumen_harus_tertaut_ke_tepat_satu_induk(): void
    {
        $this->actingAs(User::factory()->administrasi()->create())
            ->from(route('dokumen.create'))
            ->post(route('dokumen.store'), ['jenis_dokumen' => 'foto'])
            ->assertSessionHasErrors(['jenazah_id', 'laporan_kehilangan_id', 'berkas']);
    }

    public function test_peran_pengguna_baru_harus_salah_satu_nilai_sah(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->from(route('pengguna.create'))
            ->post(route('pengguna.store'), [
                'name' => 'Petugas Baru',
                'email' => 'petugas.baru@simjenazah.test',
                'role' => 'superadmin',
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertSessionHasErrors('role');
    }
}
