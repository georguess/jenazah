<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaporanKehilanganRequest;
use App\Http\Requests\UpdateLaporanKehilanganRequest;
use App\Http\Resources\DataAntemortemResource;
use App\Http\Resources\DokumenPendukungResource;
use App\Http\Resources\KecocokanIdentifikasiResource;
use App\Http\Resources\LaporanKehilanganResource;
use App\Models\DataAntemortem;
use App\Models\DokumenPendukung;
use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use App\Models\User;
use App\Support\Opsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class LaporanKehilanganController extends Controller
{
    /** Keluarga pelapor hanya menerima laporannya sendiri: disaring di kueri. */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', LaporanKehilangan::class);

        $laporan = LaporanKehilangan::terlihatOleh($request->user())->with('pencatat')
            ->cari($request->query('cari'))
            ->latest()->paginate(10)->withQueryString();

        return Inertia::render('laporan/index', [
            'laporan' => LaporanKehilanganResource::collection($laporan),
            'filter' => ['cari' => $request->query('cari', '')],
            'can' => ['tambah' => $request->user()->can('create', LaporanKehilangan::class)],
            'url' => ['index' => route('laporan.index'), 'tambah' => route('laporan.create')],
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', LaporanKehilangan::class);

        return Inertia::render('laporan/create', [
            'opsi' => $this->opsi(),
            'url' => ['simpan' => route('laporan.store'), 'batal' => route('laporan.index')],
        ]);
    }

    public function store(StoreLaporanKehilanganRequest $request): RedirectResponse
    {
        Gate::authorize('create', LaporanKehilangan::class);

        $laporan = LaporanKehilangan::create($request->validated() + ['dicatat_oleh' => $request->user()->id]);

        return redirect()->route('laporan.show', $laporan)
            ->with('sukses', "Laporan kehilangan {$laporan->nama_korban_hilang} berhasil dibuat.");
    }

    public function show(Request $request, LaporanKehilangan $laporan): Response
    {
        Gate::authorize('view', $laporan);

        $laporan->load(['pencatat', 'dataAntemortem.laporanKehilangan']);

        return Inertia::render('laporan/show', [
            'laporan' => new LaporanKehilanganResource($laporan),
            ...$this->relasiTerlihat($request->user(), $laporan),
            'url' => [
                'index' => $request->user()->can('viewAny', LaporanKehilangan::class) ? route('laporan.index') : null,
                'tambahAntemortem' => route('antemortem.create', ['laporan_kehilangan_id' => $laporan->id]),
                'tambahDokumen' => route('dokumen.create', ['laporan_kehilangan_id' => $laporan->id]),
            ],
        ]);
    }

    public function edit(Request $request, LaporanKehilangan $laporan): Response
    {
        Gate::authorize('update', $laporan);

        return Inertia::render('laporan/edit', [
            'laporan' => new LaporanKehilanganResource($laporan),
            'opsi' => $this->opsi() + ($request->user()->can('ubahStatus', LaporanKehilangan::class)
                ? ['status' => Opsi::dari(array_diff_key(LaporanKehilangan::STATUS, [LaporanKehilangan::STATUS_COCOK => 1]))] : []),
            'url' => ['simpan' => route('laporan.update', $laporan), 'batal' => route('laporan.show', $laporan)],
        ]);
    }

    public function update(UpdateLaporanKehilanganRequest $request, LaporanKehilangan $laporan): RedirectResponse
    {
        Gate::authorize('update', $laporan);

        $laporan->update($request->validated());

        return redirect()->route('laporan.show', $laporan)
            ->with('sukses', 'Laporan kehilangan berhasil diperbarui.');
    }

    public function destroy(LaporanKehilangan $laporan): RedirectResponse
    {
        Gate::authorize('delete', $laporan);

        $laporan->delete();

        return redirect()->route('laporan.index')
            ->with('sukses', "Laporan kehilangan {$laporan->nama_korban_hilang} berhasil dihapus.");
    }

    private function opsi(): array
    {
        return ['jenis_kelamin' => Opsi::dari(Jenazah::JENIS_KELAMIN)];
    }

    /** Hanya relasi yang boleh dilihat pengguna ini yang dimuat dan dikirim. */
    private function relasiTerlihat(User $user, LaporanKehilangan $laporan): array
    {
        $antemortem = $laporan->dataAntemortem;

        return [
            'antemortem' => $antemortem && $user->can('view', $antemortem) ? new DataAntemortemResource($antemortem) : null,
            'kecocokan' => $user->can('viewAny', KecocokanIdentifikasi::class)
                ? KecocokanIdentifikasiResource::collection($laporan->kecocokan()->with(['jenazah', 'laporanKehilangan', 'pengaju', 'verifikator'])->latest()->get())
                : null,
            'dokumen' => $user->can('create', DokumenPendukung::class)
                ? DokumenPendukungResource::collection($laporan->dokumenPendukung()->with(['laporanKehilangan', 'pengunggah'])->latest()->get())
                : null,
            'can' => [
                'tambahAntemortem' => ! $antemortem && $user->can('create', DataAntemortem::class) && $user->can('update', $laporan),
                'tambahDokumen' => $user->can('create', DokumenPendukung::class),
            ],
        ];
    }
}
