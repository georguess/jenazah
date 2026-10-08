<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_pendukung', function (Blueprint $table) {
            $table->id();
            // Polymorphic-lite manual: nullable FK ke dua kemungkinan induk
            // (jenazah ATAU laporan_kehilangan), dijaga oleh CHECK di bawah
            // supaya dokumen selalu tertaut ke tepat satu induk, tidak dua-duanya
            // dan tidak kosong dua-duanya.
            $table->foreignId('jenazah_id')->nullable()->constrained('jenazah')->cascadeOnDelete();
            $table->foreignId('laporan_kehilangan_id')->nullable()->constrained('laporan_kehilangan')->cascadeOnDelete();
            $table->string('jenis_dokumen', 30);
            $table->string('path_file');
            $table->foreignId('diunggah_oleh')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });

        // CHECK constraint integritas: pastikan dokumen terhubung ke tepat satu induk.
        DB::statement(
            'ALTER TABLE dokumen_pendukung ADD CONSTRAINT ck_dokumen_satu_induk '.
            'CHECK (num_nonnulls(jenazah_id, laporan_kehilangan_id) = 1)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_pendukung');
    }
};
