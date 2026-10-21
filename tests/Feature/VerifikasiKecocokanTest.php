<?php

namespace Tests\Feature;

use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifikasiKecocokanTest extends TestCase
{
    use RefreshDatabase;

    public function test_forensik_mengajukan_dan_admin_menyetujui(): void
    {
        $forensik = User::factory()->forensik()->create();
        $jenazah = Jenazah::factory()->create();
        $laporan = LaporanKehilangan::factory()->create();

        $this->actingAs($forensik)->post(route('kecocokan.store'), [
            'jenazah_id' => $jenazah->id,
            'laporan_kehilangan_id' => $laporan->id,
            'metode_kecocokan' => 'gigi',
            'skor_kecocokan' => 91.5,
        ])->assertSessionHasNoErrors()->assertSessionHas('sukses');

        $kecocokan = KecocokanIdentifikasi::sole();
        $this->assertSame($forensik->id, $kecocokan->diajukan_oleh);
        $this->assertSame(KecocokanIdentifikasi::STATUS_MENUNGGU, $kecocokan->fresh()->status_verifikasi);
        $this->assertSame(Jenazah::STATUS_PROSES, $jenazah->fresh()->status_identifikasi);

        // Forensik tidak boleh memverifikasi usulannya sendiri.
        $this->actingAs($forensik)->patch(route('kecocokan.verifikasi', $kecocokan), ['status_verifikasi' => 'disetujui'])->assertForbidden();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->patch(route('kecocokan.verifikasi', $kecocokan), ['status_verifikasi' => 'disetujui'])
            ->assertRedirect(route('kecocokan.show', $kecocokan))->assertSessionHas('sukses');

        $this->assertSame('disetujui', $kecocokan->fresh()->status_verifikasi);
        $this->assertSame($admin->id, $kecocokan->fresh()->diverifikasi_oleh);
        $this->assertSame(Jenazah::STATUS_TERIDENTIFIKASI, $jenazah->fresh()->status_identifikasi);
        $this->assertSame(LaporanKehilangan::STATUS_COCOK, $laporan->fresh()->status);

        // Setelah diverifikasi, usulan terkunci: tidak bisa diverifikasi ulang atau diubah.
        $this->actingAs($admin)->patch(route('kecocokan.verifikasi', $kecocokan), ['status_verifikasi' => 'ditolak', 'catatan_verifikasi' => 'x'])->assertForbidden();
        $this->actingAs($forensik)->get(route('kecocokan.edit', $kecocokan))->assertForbidden();
    }

    public function test_penolakan_wajib_beralasan(): void
    {
        $kecocokan = KecocokanIdentifikasi::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->from(route('kecocokan.show', $kecocokan))
            ->patch(route('kecocokan.verifikasi', $kecocokan), ['status_verifikasi' => 'ditolak'])
            ->assertSessionHasErrors('catatan_verifikasi');
    }

    public function test_forensik_hanya_mengubah_usulan_miliknya(): void
    {
        $kecocokan = KecocokanIdentifikasi::factory()->create();
        $forensikLain = User::factory()->forensik()->create();

        $this->actingAs($forensikLain)->put(route('kecocokan.update', $kecocokan), ['metode_kecocokan' => 'dna', 'skor_kecocokan' => 70])->assertForbidden();
        $this->actingAs($kecocokan->pengaju)->put(route('kecocokan.update', $kecocokan), ['metode_kecocokan' => 'dna', 'skor_kecocokan' => 70])
            ->assertRedirect(route('kecocokan.show', $kecocokan));
    }
}
