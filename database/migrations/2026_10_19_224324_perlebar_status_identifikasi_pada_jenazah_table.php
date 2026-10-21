<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migrasi BARU — memperbaiki cacat pada migrasi lama tanpa menyuntingnya.
 *
 * Kolom jenazah.status_identifikasi bertipe varchar(20), padahal nilai
 * bawaannya 'belum_teridentifikasi' panjangnya 21 karakter. Akibatnya setiap
 * INSERT yang mengandalkan nilai bawaan gagal ("value too long"). Cacat ini
 * tidak terlihat sebelumnya karena kode lama belum pernah dijalankan dan
 * seeder lama selalu mengisi status secara eksplisit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenazah', function (Blueprint $table) {
            $table->string('status_identifikasi', 30)->default('belum_teridentifikasi')->change();
        });
    }

    public function down(): void
    {
        Schema::table('jenazah', function (Blueprint $table) {
            $table->string('status_identifikasi', 20)->default('belum_teridentifikasi')->change();
        });
    }
};
