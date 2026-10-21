<?php

namespace App\Http\Middleware;

use App\Models\DataAntemortem;
use App\Models\DataPostmortem;
use App\Models\DokumenPendukung;
use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Props bersama untuk setiap halaman. Hanya berisi data yang boleh dilihat
     * pengguna: tidak ada rahasia, dan kolom `role` TIDAK dikirim sebagai dasar
     * keputusan — kewenangan dikirim sebagai hasil Gate/Policy (menu, can).
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'label_peran' => $user->labelPeran(),
                ] : null,
            ],
            'flash' => [
                'sukses' => fn () => $request->session()->get('sukses'),
                'galat' => fn () => $request->session()->get('galat'),
            ],
            'rute' => fn () => $this->rute(),
            'menu' => fn () => $user ? $this->menu($user) : [],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * Item bilah samping dibangun di server dari hasil Policy/Gate yang sama
     * dengan yang menjaga controller. Menyembunyikan menu hanyalah kesopanan;
     * pembatasan sebenarnya tetap middleware `peran` + Gate::authorize.
     */
    private function menu(User $user): array
    {
        $labelLaporan = match ($user->role) {
            User::ROLE_KELUARGA_PELAPOR => 'Laporan Saya',
            User::ROLE_PETUGAS_ADMINISTRASI => 'Laporan',
            default => 'Laporan Kehilangan',
        };

        $kandidat = [
            ['judul' => 'Dasbor', 'ikon' => 'dasbor', 'rute' => 'dashboard', 'boleh' => true],
            ['judul' => 'Jenazah', 'ikon' => 'jenazah', 'rute' => 'jenazah.index', 'boleh' => $user->can('viewAny', Jenazah::class)],
            ['judul' => $labelLaporan, 'ikon' => 'laporan', 'rute' => 'laporan.index', 'boleh' => $user->can('viewAny', LaporanKehilangan::class)],
            ['judul' => 'Data Antemortem Saya', 'ikon' => 'antemortem', 'rute' => 'antemortem.index', 'boleh' => $user->can('viewAny', DataAntemortem::class)],
            ['judul' => 'Postmortem', 'ikon' => 'postmortem', 'rute' => 'postmortem.index', 'boleh' => $user->can('viewAny', DataPostmortem::class)],
            ['judul' => 'Kecocokan', 'ikon' => 'kecocokan', 'rute' => 'kecocokan.index', 'boleh' => $user->can('viewAny', KecocokanIdentifikasi::class)],
            ['judul' => 'Dokumen', 'ikon' => 'dokumen', 'rute' => 'dokumen.index', 'boleh' => $user->can('viewAny', DokumenPendukung::class)],
            ['judul' => 'Pengguna', 'ikon' => 'pengguna', 'rute' => 'pengguna.index', 'boleh' => $user->can('kelola-pengguna')],
        ];

        return collect($kandidat)
            ->filter(fn (array $item) => $item['boleh'])
            ->map(fn (array $item) => [
                'judul' => $item['judul'],
                'ikon' => $item['ikon'],
                'href' => route($item['rute']),
            ])
            ->values()
            ->all();
    }

    /** Alamat bernama yang dipakai tata letak dan halaman autentikasi starter kit. */
    private function rute(): array
    {
        $nama = [
            'home', 'dashboard', 'login', 'register', 'logout',
            'password.request', 'password.email', 'password.update', 'password.confirm',
            'verification.send', 'profile.edit', 'profile.update', 'profile.destroy',
            'security.edit', 'user-password.update', 'appearance.edit',
        ];

        return collect($nama)
            ->filter(fn (string $n) => Route::has($n))
            ->mapWithKeys(fn (string $n) => [$n => route($n)])
            ->all();
    }
}
