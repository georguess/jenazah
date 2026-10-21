<?php

namespace App\Http\Controllers;

use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Dasbor untuk semua peran. Statistik menyeluruh hanya dihitung bila Gate
     * "melihat-statistik" mengizinkan (admin) — pengguna lain tidak menerimanya.
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('dashboard', [
            'statistik' => Gate::allows('melihat-statistik') ? $this->statistik() : null,
            'laporanSaya' => LaporanKehilangan::terlihatOleh($request->user())
                ->where('dicatat_oleh', $request->user()->id)
                ->latest()->take(5)->get(['id', 'nama_korban_hilang', 'status'])
                ->map(fn ($l) => [
                    'id' => $l->id,
                    'nama_korban_hilang' => $l->nama_korban_hilang,
                    'status_label' => LaporanKehilangan::STATUS[$l->status] ?? $l->status,
                    'url' => route('laporan.show', $l),
                ]),
        ]);
    }

    private function statistik(): array
    {
        return [
            ['judul' => 'Jenazah tercatat', 'nilai' => Jenazah::count()],
            ['judul' => 'Belum teridentifikasi', 'nilai' => Jenazah::where('status_identifikasi', '!=', Jenazah::STATUS_TERIDENTIFIKASI)->count()],
            ['judul' => 'Laporan kehilangan aktif', 'nilai' => LaporanKehilangan::where('status', LaporanKehilangan::STATUS_AKTIF)->count()],
            ['judul' => 'Kecocokan menunggu verifikasi', 'nilai' => KecocokanIdentifikasi::where('status_verifikasi', KecocokanIdentifikasi::STATUS_MENUNGGU)->count(), 'url' => route('kecocokan.index', ['status' => KecocokanIdentifikasi::STATUS_MENUNGGU])],
        ];
    }
}
