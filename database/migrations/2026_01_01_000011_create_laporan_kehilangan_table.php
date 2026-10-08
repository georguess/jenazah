<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kehilangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->string('no_hp_pelapor', 20);
            $table->string('hubungan_dengan_korban', 50);
            $table->string('nama_korban_hilang');
            $table->string('jenis_kelamin', 20)->nullable();
            $table->unsignedTinyInteger('usia_perkiraan')->nullable();
            $table->text('ciri_fisik')->nullable();
            $table->date('tanggal_hilang');
            $table->string('lokasi_terakhir');
            $table->string('status', 20)->default('aktif');
            // dicatat_oleh nullable + set null: laporan bisa diinput sendiri oleh
            // keluarga (belum tentu punya akun user), atau dicatat petugas.
            // Jika akun petugas dihapus, laporan tetap ada (bukti tidak boleh hilang).
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kehilangan');
    }
};
