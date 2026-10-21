<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migrasi BARU. Tabel kecocokan_identifikasi hanya mencatat siapa yang
 * MEMVERIFIKASI, tidak mencatat siapa yang MENGAJUKAN. Tanpa kolom ini
 * Policy tidak dapat membatasi "petugas forensik hanya mengubah usulan
 * miliknya", dan jejak audit pengajuan hilang.
 *
 * nullable: baris lama (hasil seeder/sebelum migrasi) tidak punya pengaju.
 * restrictOnDelete: sama dengan diperiksa_oleh — pengaju usulan identifikasi
 * adalah bagian rantai bukti, akunnya tidak boleh terhapus diam-diam.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kecocokan_identifikasi', function (Blueprint $table) {
            $table->foreignId('diajukan_oleh')->nullable()->after('status_verifikasi')
                ->constrained('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kecocokan_identifikasi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('diajukan_oleh');
        });
    }
};
