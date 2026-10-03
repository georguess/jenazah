<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SI Manajemen Jenazah dan Identifikasi Terintegrasi
|--------------------------------------------------------------------------
| Baseproject: hanya route dasar untuk memverifikasi migrasi & seeder.
| Peran "antarmuka dan komponen" bertugas mengganti/menambah route di sini.
*/

Route::get('/', function () {
    return view('welcome', [
        'jumlahJenazah' => \App\Models\Jenazah::count(),
        'jumlahLaporan' => \App\Models\LaporanKehilangan::count(),
        'jumlahKecocokan' => \App\Models\KecocokanIdentifikasi::count(),
    ]);
})->name('home');
