<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_antemortem', function (Blueprint $table) {
            $table->id();
            // Relasi 1-ke-1 terhadap laporan_kehilangan: satu laporan hanya
            // punya satu berkas data antemortem yang terus diperbarui/dilengkapi.
            // cascade: jika laporan dihapus (misal salah input), data ikut terhapus
            // karena data ini tidak punya makna berdiri sendiri tanpa laporannya.
            $table->foreignId('laporan_kehilangan_id')
                ->unique()
                ->constrained('laporan_kehilangan')
                ->cascadeOnDelete();
            $table->text('gigi_geligi')->nullable();
            $table->text('ciri_khusus')->nullable();
            $table->string('golongan_darah', 5)->nullable();
            $table->decimal('tinggi_badan_cm', 5, 1)->nullable();
            $table->decimal('berat_badan_kg', 5, 1)->nullable();
            $table->text('riwayat_medis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_antemortem');
    }
};
