<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataPostmortemRequest;
use App\Http\Requests\UpdateDataPostmortemRequest;
use App\Http\Resources\DataPostmortemResource;
use App\Models\DataAntemortem;
use App\Models\DataPostmortem;
use App\Models\Jenazah;
use App\Support\Opsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DataPostmortemController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', DataPostmortem::class);

        $postmortem = DataPostmortem::with(['jenazah', 'pemeriksa'])->latest()->paginate(10);

        return Inertia::render('postmortem/index', [
            'postmortem' => DataPostmortemResource::collection($postmortem),
            'can' => ['tambah' => $request->user()->can('create', DataPostmortem::class)],
            'url' => ['index' => route('postmortem.index'), 'tambah' => route('postmortem.create')],
        ]);
    }

    public function create(Request $request): Response
    {
        Gate::authorize('create', DataPostmortem::class);

        $jenazah = Jenazah::doesntHave('dataPostmortem')->orderBy('kode_jenazah')->get(['id', 'kode_jenazah', 'lokasi_ditemukan']);

        return Inertia::render('postmortem/create', [
            'opsi' => $this->opsi() + ['jenazah' => Opsi::model($jenazah, fn ($j) => "{$j->kode_jenazah} — {$j->lokasi_ditemukan}")],
            'bawaan' => ['jenazah_id' => $request->integer('jenazah_id') ?: '', 'tanggal_pemeriksaan' => now()->toDateString()],
            'url' => ['simpan' => route('postmortem.store'), 'batal' => url()->previous(route('dashboard'))],
        ]);
    }

    public function store(StoreDataPostmortemRequest $request): RedirectResponse
    {
        Gate::authorize('create', DataPostmortem::class);

        $postmortem = DataPostmortem::create($request->validated() + ['diperiksa_oleh' => $request->user()->id]);

        return redirect()->route('postmortem.show', $postmortem)
            ->with('sukses', 'Hasil pemeriksaan postmortem berhasil disimpan.');
    }

    public function show(DataPostmortem $postmortem): Response
    {
        Gate::authorize('view', $postmortem);

        return Inertia::render('postmortem/show', [
            'postmortem' => new DataPostmortemResource($postmortem->load(['jenazah', 'pemeriksa'])),
        ]);
    }

    public function edit(DataPostmortem $postmortem): Response
    {
        Gate::authorize('update', $postmortem);

        return Inertia::render('postmortem/edit', [
            'postmortem' => new DataPostmortemResource($postmortem->load(['jenazah', 'pemeriksa'])),
            'opsi' => $this->opsi(),
            'url' => ['simpan' => route('postmortem.update', $postmortem), 'batal' => route('postmortem.show', $postmortem)],
        ]);
    }

    public function update(UpdateDataPostmortemRequest $request, DataPostmortem $postmortem): RedirectResponse
    {
        Gate::authorize('update', $postmortem);

        $postmortem->update($request->validated());

        return redirect()->route('postmortem.show', $postmortem)
            ->with('sukses', 'Hasil pemeriksaan postmortem berhasil diperbarui.');
    }

    public function destroy(DataPostmortem $postmortem): RedirectResponse
    {
        Gate::authorize('delete', $postmortem);

        $postmortem->delete();

        return redirect()->route('jenazah.show', $postmortem->jenazah_id)
            ->with('sukses', 'Hasil pemeriksaan postmortem berhasil dihapus.');
    }

    private function opsi(): array
    {
        return ['golongan_darah' => Opsi::dari(DataAntemortem::GOLONGAN_DARAH)];
    }
}
