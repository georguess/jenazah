<?php

namespace Tests\Feature;

use App\Models\DataPostmortem;
use App\Models\Jenazah;
use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/** Aksi tulis selalu diakhiri pengalihan + pesan flash; aksi baca diakhiri halaman. */
class PostRedirectGetTest extends TestCase
{
    use RefreshDatabase;

    public function test_tujuh_aksi_jenazah(): void
    {
        $petugas = User::factory()->administrasi()->create();
        $this->actingAs($petugas);

        $this->get(route('jenazah.create'))->assertInertia(fn (Assert $p) => $p->component('jenazah/create'));

        $this->post(route('jenazah.store'), [
            'kode_jenazah' => 'JZ-2026-777',
            'tanggal_ditemukan' => now()->toDateString(),
            'lokasi_ditemukan' => 'Pesisir Pantai Mutun, Kabupaten Pesawaran',
            'kondisi_jenazah' => 'utuh',
            'jenis_kelamin' => 'perempuan',
        ])->assertRedirect(route('jenazah.show', $jenazah = Jenazah::firstWhere('kode_jenazah', 'JZ-2026-777')))
            ->assertSessionHas('sukses');

        $this->assertSame($petugas->id, $jenazah->dicatat_oleh);
        $this->assertSame(Jenazah::STATUS_BELUM, $jenazah->fresh()->status_identifikasi);

        // Halaman berikutnya (GET) menampilkan pesan flash lewat props bersama.
        $this->get(route('jenazah.show', $jenazah))->assertInertia(fn (Assert $p) => $p
            ->component('jenazah/show')
            ->where('jenazah.can.ubah', true));

        $this->get(route('jenazah.index'))->assertInertia(fn (Assert $p) => $p->component('jenazah/index')->has('jenazah.data', 1));
        $this->get(route('jenazah.edit', $jenazah))->assertInertia(fn (Assert $p) => $p->component('jenazah/edit'));

        $this->put(route('jenazah.update', $jenazah), [
            'kode_jenazah' => 'JZ-2026-777',
            'tanggal_ditemukan' => now()->toDateString(),
            'lokasi_ditemukan' => 'Lokasi diperbarui',
            'kondisi_jenazah' => 'sebagian',
            'status_identifikasi' => 'teridentifikasi', // tidak boleh ikut tersimpan
        ])->assertRedirect(route('jenazah.show', $jenazah))->assertSessionHas('sukses');

        $this->assertSame('Lokasi diperbarui', $jenazah->fresh()->lokasi_ditemukan);
        $this->assertSame(Jenazah::STATUS_BELUM, $jenazah->fresh()->status_identifikasi);

        $this->delete(route('jenazah.destroy', $jenazah))->assertRedirect(route('jenazah.index'))->assertSessionHas('sukses');
        $this->assertModelMissing($jenazah);
    }

    public function test_pesan_flash_dibagikan_ke_halaman_lewat_share(): void
    {
        $this->actingAs(User::factory()->administrasi()->create())
            ->withSession(['sukses' => 'Jenazah JZ-1 berhasil dicatat.'])
            ->get(route('jenazah.index'))
            ->assertInertia(fn (Assert $p) => $p->where('flash.sukses', 'Jenazah JZ-1 berhasil dicatat.'));
    }

    public function test_administrasi_tidak_boleh_menghapus_jenazah_yang_sudah_diperiksa(): void
    {
        $postmortem = DataPostmortem::factory()->create();
        $administrasi = User::factory()->administrasi()->create();

        $this->actingAs($administrasi)->delete(route('jenazah.destroy', $postmortem->jenazah_id))->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->delete(route('jenazah.destroy', $postmortem->jenazah_id))->assertRedirect();
        $this->assertModelMissing($postmortem);
    }

    public function test_laporan_keluarga_dicatat_atas_namanya(): void
    {
        $keluarga = User::factory()->keluarga()->create();

        $this->actingAs($keluarga)->post(route('laporan.store'), [
            'nama_pelapor' => $keluarga->name,
            'no_hp_pelapor' => '081234567890',
            'hubungan_dengan_korban' => 'anak kandung',
            'nama_korban_hilang' => 'Raden Mas Muhammad Fadhlurrahman Wicaksono Adiningrat Putra',
            'tanggal_hilang' => now()->subDays(3)->toDateString(),
            'lokasi_terakhir' => 'Desa Sukajaya, Way Lima',
        ])->assertSessionHasNoErrors()->assertSessionHas('sukses');

        $laporan = LaporanKehilangan::sole();
        $this->assertSame($keluarga->id, $laporan->dicatat_oleh);
        $this->assertSame('aktif', $laporan->status);
    }

    public function test_unggah_dan_unduh_dokumen_pendukung(): void
    {
        Storage::fake();
        $jenazah = Jenazah::factory()->create();
        $this->actingAs($petugas = User::factory()->administrasi()->create());

        $this->post(route('dokumen.store'), [
            'jenazah_id' => $jenazah->id,
            'jenis_dokumen' => 'foto',
            'berkas' => UploadedFile::fake()->image('wajah.jpg'),
        ])->assertRedirect()->assertSessionHas('sukses');

        $dokumen = $jenazah->dokumenPendukung()->sole();
        Storage::assertExists($dokumen->path_file);
        $this->assertSame($petugas->id, $dokumen->diunggah_oleh);

        $this->get(route('dokumen.unduh', $dokumen))->assertOk()->assertDownload();
        $this->actingAs(User::factory()->keluarga()->create())->get(route('dokumen.unduh', $dokumen))->assertForbidden();
    }

    public function test_registrasi_publik_selalu_menjadi_keluarga_pelapor(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Warga Baru',
            'email' => 'warga@contoh.test',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin', // dicoba disusupkan
        ]);

        $this->assertSame(User::ROLE_KELUARGA_PELAPOR, User::firstWhere('email', 'warga@contoh.test')->role);
    }
}
