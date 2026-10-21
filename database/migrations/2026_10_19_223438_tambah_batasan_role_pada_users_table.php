<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Migrasi BARU (migrasi users lama tidak disunting).
 *
 * 1. Kolom tetap bernama `role` (tidak di-rename menjadi `peran`) karena sudah
 *    dipakai model, seeder, dan dokumen; rename hanya menambah risiko tanpa
 *    manfaat fungsional.
 * 2. Nilai dibatasi dengan CHECK, bukan enum native PostgreSQL (pertemuan 3:
 *    hindari enum bawaan) — menambah peran cukup dengan migrasi CHECK baru.
 * 3. Default diubah ke peran paling rendah (keluarga_pelapor). Default lama
 *    `petugas_administrasi` berbahaya: akun dari halaman registrasi publik
 *    akan otomatis menjadi petugas.
 */
return new class extends Migration
{
    private const PERAN = ['admin', 'petugas_forensik', 'petugas_administrasi', 'keluarga_pelapor'];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('keluarga_pelapor')->change();
        });

        $daftar = "'".implode("','", self::PERAN)."'";
        DB::statement("ALTER TABLE users ADD CONSTRAINT ck_users_role CHECK (role IN ({$daftar}))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS ck_users_role');

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('petugas_administrasi')->change();
        });
    }
};
