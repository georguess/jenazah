<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_postmortem', function (Blueprint $table) {
            $table->id();
            // 1-ke-1 terhadap jenazah, cascade dengan alasan yang sama seperti antemortem.
            $table->foreignId('jenazah_id')
                ->unique()
                ->constrained('jenazah')
                ->cascadeOnDelete();
            $table->text('gigi_geligi')->nullable();
            $table->text('ciri_khusus')->nullable();
            $table->string('golongan_darah', 5)->nullable();
            $table->decimal('estimasi_tinggi_badan_cm', 5, 1)->nullable();
            $table->decimal('estimasi_berat_badan_kg', 5, 1)->nullable();
            $table->string('sidik_jari_path')->nullable();
            $table->string('kode_sampel_dna', 50)->nullable();
            // restrict: dokter forensik pemeriksa adalah bukti hukum (chain of custody),
            // tidak boleh hilang meski akunnya dihapus dari sistem.
            $table->foreignId('diperiksa_oleh')->constrained('users')->restrictOnDelete();
            $table->date('tanggal_pemeriksaan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_postmortem');
    }
};
