<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDokumenPendukungRequest;
use App\Http\Resources\DokumenPendukungResource;
use App\Models\DokumenPendukung;
use App\Models\Jenazah;
use App\Models\LaporanKehilangan;
use App\Support\Opsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokumenPendukungController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', DokumenPendukung::class);

        $dokumen = DokumenPendukung::with(['jenazah', 'laporanKehilangan', 'pengunggah'])->latest()->paginate(10);

        return Inertia::render('dokumen/index', [
            'dokumen' => DokumenPendukungResource::collection($dokumen),
            'can' => ['tambah' => $request->user()->can('create', DokumenPendukung::class)],
            'url' => ['index' => route('dokumen.index'), 'tambah' => route('dokumen.create')],
        ]);
    }

    public function create(Request $request): Response
    {
        Gate::authorize('create', DokumenPendukung::class);

        return Inertia::render('dokumen/create', [
            'opsi' => [
                'jenis' => Opsi::dari(DokumenPendukung::JENIS),
                'jenazah' => Opsi::model(Jenazah::orderBy('kode_jenazah')->get(['id', 'kode_jenazah']), fn ($j) => $j->kode_jenazah),
                'laporan' => Opsi::model(LaporanKehilangan::orderBy('nama_korban_hilang')->get(['id', 'nama_korban_hilang']), fn ($l) => $l->nama_korban_hilang),
            ],
            'bawaan' => [
                'jenazah_id' => $request->integer('jenazah_id') ?: '',
                'laporan_kehilangan_id' => $request->integer('laporan_kehilangan_id') ?: '',
            ],
            'url' => ['simpan' => route('dokumen.store'), 'batal' => url()->previous(route('dashboard'))],
        ]);
    }

    /** Berkas disimpan di disk privat (storage/app/private), bukan folder public. */
    public function store(StoreDokumenPendukungRequest $request): RedirectResponse
    {
        Gate::authorize('create', DokumenPendukung::class);

        $dokumen = DokumenPendukung::create($request->safe()->except('berkas') + [
            'path_file' => $request->file('berkas')->store('dokumen-pendukung'),
            'diunggah_oleh' => $request->user()->id,
        ]);

        return redirect()->route('dokumen.show', $dokumen)->with('sukses', 'Dokumen pendukung berhasil diunggah.');
    }

    public function show(DokumenPendukung $dokumen): Response
    {
        Gate::authorize('view', $dokumen);

        return Inertia::render('dokumen/show', [
            'dokumen' => new DokumenPendukungResource($dokumen->load(['jenazah', 'laporanKehilangan', 'pengunggah'])),
        ]);
    }

    /** Unduhan melewati Policy yang sama; tidak ada tautan langsung ke berkas. */
    public function unduh(DokumenPendukung $dokumen): StreamedResponse
    {
        Gate::authorize('view', $dokumen);

        return Storage::download($dokumen->path_file, "dokumen-{$dokumen->id}-{$dokumen->jenis_dokumen}.".pathinfo($dokumen->path_file, PATHINFO_EXTENSION));
    }

    public function destroy(DokumenPendukung $dokumen): RedirectResponse
    {
        Gate::authorize('delete', $dokumen);

        $dokumen->delete();
        Storage::delete($dokumen->path_file);

        return redirect()->to($dokumen->jenazah_id ? route('jenazah.show', $dokumen->jenazah_id) : route('laporan.show', $dokumen->laporan_kehilangan_id))
            ->with('sukses', 'Dokumen pendukung berhasil dihapus.');
    }
}
