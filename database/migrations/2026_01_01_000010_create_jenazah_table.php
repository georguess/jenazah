<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenazah', function (Blueprint $table) {
            $table->id();
            // kode_jenazah: identitas fisik/label kantong jenazah di lapangan,
            // dibuat unik agar tidak ada duplikasi pencatatan barang bukti yang sama.
            $table->string('kode_jenazah', 30)->unique();
            $table->date('tanggal_ditemukan');
            $table->string('lokasi_ditemukan');
            $table->string('jenis_kelamin', 20)->nullable();
            $table->unsignedTinyInteger('perkiraan_usia')->nullable();
            $table->string('kondisi_jenazah', 20)->default('tidak_dikenali');
            // status_identifikasi menjadi acuan alur kerja (workflow) lintas tabel
            // (laporan, kecocokan) sehingga disimpan sebagai kolom eksplisit,
            // bukan diturunkan dari relasi, supaya query status cepat tanpa join.
            $table->string('status_identifikasi', 20)->default('belum_teridentifikasi');
            $table->text('catatan')->nullable();
            // dicatat_oleh: restrict agar riwayat pencatatan petugas tidak hilang
            // walau data user lain berubah; user tidak boleh dihapus jika masih
            // menjadi pencatat data jenazah (integritas audit trail forensik).
            $table->foreignId('dicatat_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('status_identifikasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenazah');
    }
};
