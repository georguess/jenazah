<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataAntemortemRequest;
use App\Http\Requests\UpdateDataAntemortemRequest;
use App\Http\Resources\DataAntemortemResource;
use App\Models\DataAntemortem;
use App\Models\LaporanKehilangan;
use App\Support\Opsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DataAntemortemController extends Controller
{
    /** "Data Antemortem Saya" — disaring di kueri lewat laporan milik pengguna. */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', DataAntemortem::class);

        $antemortem = DataAntemortem::terlihatOleh($request->user())
            ->with('laporanKehilangan')->latest()->paginate(10);

        return Inertia::render('antemortem/index', [
            'antemortem' => DataAntemortemResource::collection($antemortem),
            'can' => ['tambah' => $request->user()->can('create', DataAntemortem::class)],
            'url' => ['index' => route('antemortem.index'), 'tambah' => route('antemortem.create')],
        ]);
    }

    public function create(Request $request): Response
    {
        Gate::authorize('create', DataAntemortem::class);

        return Inertia::render('antemortem/create', [
            'opsi' => $this->opsi() + ['laporan' => $this->laporanTanpaAntemortem($request)],
            'bawaan' => ['laporan_kehilangan_id' => $request->integer('laporan_kehilangan_id') ?: ''],
            'url' => ['simpan' => route('antemortem.store'), 'batal' => url()->previous(route('dashboard'))],
        ]);
    }

    public function store(StoreDataAntemortemRequest $request): RedirectResponse
    {
        Gate::authorize('create', DataAntemortem::class);

        $antemortem = DataAntemortem::create($request->validated());

        return redirect()->route('laporan.show', $antemortem->laporan_kehilangan_id)
            ->with('sukses', 'Data antemortem berhasil disimpan.');
    }

    public function show(DataAntemortem $antemortem): Response
    {
        $antemortem->load('laporanKehilangan');
        Gate::authorize('view', $antemortem);

        return Inertia::render('antemortem/show', [
            'antemortem' => new DataAntemortemResource($antemortem),
        ]);
    }

    public function edit(DataAntemortem $antemortem): Response
    {
        $antemortem->load('laporanKehilangan');
        Gate::authorize('update', $antemortem);

        return Inertia::render('antemortem/edit', [
            'antemortem' => new DataAntemortemResource($antemortem),
            'opsi' => $this->opsi(),
            'url' => ['simpan' => route('antemortem.update', $antemortem), 'batal' => route('antemortem.show', $antemortem)],
        ]);
    }

    public function update(UpdateDataAntemortemRequest $request, DataAntemortem $antemortem): RedirectResponse
    {
        $antemortem->load('laporanKehilangan');
        Gate::authorize('update', $antemortem);

        $antemortem->update($request->validated());

        return redirect()->route('antemortem.show', $antemortem)
            ->with('sukses', 'Data antemortem berhasil diperbarui.');
    }

    public function destroy(DataAntemortem $antemortem): RedirectResponse
    {
        Gate::authorize('delete', $antemortem);

        $antemortem->delete();

        return redirect()->route('laporan.show', $antemortem->laporan_kehilangan_id)
            ->with('sukses', 'Data antemortem berhasil dihapus.');
    }

    private function opsi(): array
    {
        return ['golongan_darah' => Opsi::dari(DataAntemortem::GOLONGAN_DARAH)];
    }

    /** Pilihan laporan: milik pengguna (bila keluarga), masih aktif, dan belum punya antemortem. */
    private function laporanTanpaAntemortem(Request $request): array
    {
        $laporan = LaporanKehilangan::terlihatOleh($request->user())
            ->where('status', LaporanKehilangan::STATUS_AKTIF)
            ->doesntHave('dataAntemortem')
            ->orderBy('nama_korban_hilang')->get(['id', 'nama_korban_hilang', 'tanggal_hilang']);

        return Opsi::model($laporan, fn ($l) => "{$l->nama_korban_hilang} (hilang {$l->tanggal_hilang->format('d-m-Y')})");
    }
}
