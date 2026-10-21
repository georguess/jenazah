<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataAntemortemController;
use App\Http\Controllers\DataPostmortemController;
use App\Http\Controllers\DokumenPendukungController;
use App\Http\Controllers\JenazahController;
use App\Http\Controllers\KecocokanIdentifikasiController;
use App\Http\Controllers\LaporanKehilanganController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute aplikasi
|--------------------------------------------------------------------------
| - Tidak ada closure berisi kueri; semua aksi ada di controller.
| - Tidak ada rute di luar kelompok middleware. Rute autentikasi
|   (login, register, reset sandi) didaftarkan Fortify dengan middleware guest.
| - Kelompok `peran:` menjawab "boleh membuka halaman ini?"; Policy di
|   controller menjawab "boleh melakukan ini pada data itu?".
| - Parameter dibatasi angka agar /laporan/create tidak tertangkap sebagai
|   /laporan/{laporan} ketika satu sumber daya terbagi ke beberapa kelompok.
*/

Route::patterns([
    'jenazah' => '[0-9]+',
    'laporan' => '[0-9]+',
    'antemortem' => '[0-9]+',
    'postmortem' => '[0-9]+',
    'kecocokan' => '[0-9]+',
    'dokumen' => '[0-9]+',
    'pengguna' => '[0-9]+',
]);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/', '/dashboard')->name('home');
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Semua peran: rincian laporan & antemortem (keluarga hanya miliknya — Policy).
    Route::middleware('peran:admin,petugas_administrasi,petugas_forensik,keluarga_pelapor')->group(function () {
        Route::resource('laporan', LaporanKehilanganController::class)->only(['show']);
        Route::resource('antemortem', DataAntemortemController::class)->only(['show']);
    });

    // Pencatat laporan kehilangan: admin, petugas administrasi, keluarga pelapor.
    Route::middleware('peran:admin,petugas_administrasi,keluarga_pelapor')->group(function () {
        Route::resource('laporan', LaporanKehilanganController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::resource('antemortem', DataAntemortemController::class)->only(['create', 'store', 'edit', 'update']);
    });

    // Keluarga pelapor: daftar "Data Antemortem Saya".
    Route::middleware('peran:keluarga_pelapor')->group(function () {
        Route::resource('antemortem', DataAntemortemController::class)->only(['index']);
    });

    // Staf: melihat jenazah (petugas forensik hanya melihat).
    Route::middleware('peran:admin,petugas_administrasi,petugas_forensik')->group(function () {
        Route::resource('jenazah', JenazahController::class)->only(['index', 'show']);
    });

    // Admin + petugas administrasi: mengelola jenazah, menghapus laporan, dokumen.
    Route::middleware('peran:admin,petugas_administrasi')->group(function () {
        Route::resource('jenazah', JenazahController::class)->except(['index', 'show']);
        Route::resource('laporan', LaporanKehilanganController::class)->only(['destroy']);
        Route::resource('antemortem', DataAntemortemController::class)->only(['destroy']);
        Route::resource('dokumen', DokumenPendukungController::class)->except(['edit', 'update'])
            ->parameters(['dokumen' => 'dokumen']);
        Route::get('dokumen/{dokumen}/unduh', [DokumenPendukungController::class, 'unduh'])->name('dokumen.unduh');
    });

    // Admin + petugas forensik: postmortem dan usulan kecocokan.
    Route::middleware('peran:admin,petugas_forensik')->group(function () {
        Route::resource('postmortem', DataPostmortemController::class);
        Route::resource('kecocokan', KecocokanIdentifikasiController::class);
    });

    // Admin saja: verifikasi akhir dan kelola pengguna.
    Route::middleware('peran:admin')->group(function () {
        Route::patch('kecocokan/{kecocokan}/verifikasi', [KecocokanIdentifikasiController::class, 'verifikasi'])
            ->name('kecocokan.verifikasi');
        Route::resource('pengguna', UserController::class);
    });
});

require __DIR__.'/settings.php';
