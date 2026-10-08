<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kecocokan_identifikasi', function (Blueprint $table) {
            $table->id();
            // Many-to-many diwakili tabel eksplisit (bukan pivot polos) karena
            // relasi ini punya atribut sendiri (skor, metode, status verifikasi)
            // dan satu jenazah bisa dicoba-cocokkan dengan banyak laporan (dan sebaliknya)
            // sebelum satu di antaranya diverifikasi/disetujui.
            $table->foreignId('jenazah_id')->constrained('jenazah')->cascadeOnDelete();
            $table->foreignId('laporan_kehilangan_id')->constrained('laporan_kehilangan')->cascadeOnDelete();
            $table->string('metode_kecocokan', 20);
            // decimal, bukan float, agar skor 0-100 dengan 2 desimal konsisten
            // saat dibandingkan/diurutkan (float rawan galat pembulatan biner).
            $table->decimal('skor_kecocokan', 5, 2)->default(0);
            $table->string('status_verifikasi', 20)->default('menunggu');
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamps();

            // Mencegah pasangan jenazah-laporan yang sama dicatat berulang dengan
            // metode yang sama (integritas: satu metode kecocokan per pasangan).
            $table->unique(['jenazah_id', 'laporan_kehilangan_id', 'metode_kecocokan'], 'uq_kecocokan_pasangan_metode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kecocokan_identifikasi');
    }
};
