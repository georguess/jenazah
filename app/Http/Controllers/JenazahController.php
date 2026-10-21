<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJenazahRequest;
use App\Http\Requests\UpdateJenazahRequest;
use App\Http\Resources\DataPostmortemResource;
use App\Http\Resources\DokumenPendukungResource;
use App\Http\Resources\JenazahResource;
use App\Http\Resources\KecocokanIdentifikasiResource;
use App\Models\DataPostmortem;
use App\Models\DokumenPendukung;
use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\User;
use App\Support\Opsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class JenazahController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Jenazah::class);

        $jenazah = Jenazah::with('pencatat')->withExists('dataPostmortem')
            ->cari($request->query('cari'))
            ->latest()->paginate(10)->withQueryString();

        return Inertia::render('jenazah/index', [
            'jenazah' => JenazahResource::collection($jenazah),
            'filter' => ['cari' => $request->query('cari', '')],
            'can' => ['tambah' => $request->user()->can('create', Jenazah::class)],
            'url' => ['index' => route('jenazah.index'), 'tambah' => route('jenazah.create')],
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Jenazah::class);

        return Inertia::render('jenazah/create', [
            'opsi' => $this->opsi(),
            'url' => ['simpan' => route('jenazah.store'), 'batal' => route('jenazah.index')],
        ]);
    }

    public function store(StoreJenazahRequest $request): RedirectResponse
    {
        Gate::authorize('create', Jenazah::class);

        $jenazah = Jenazah::create($request->validated() + ['dicatat_oleh' => $request->user()->id]);

        return redirect()->route('jenazah.show', $jenazah)
            ->with('sukses', "Jenazah {$jenazah->kode_jenazah} berhasil dicatat.");
    }

    public function show(Request $request, Jenazah $jenazah): Response
    {
        Gate::authorize('view', $jenazah);

        $jenazah->load(['pencatat', 'dataPostmortem']);

        return Inertia::render('jenazah/show', [
            'jenazah' => new JenazahResource($jenazah),
            ...$this->relasiTerlihat($request->user(), $jenazah),
            'url' => [
                'index' => route('jenazah.index'),
                'tambahPostmortem' => route('postmortem.create', ['jenazah_id' => $jenazah->id]),
                'tambahDokumen' => route('dokumen.create', ['jenazah_id' => $jenazah->id]),
            ],
        ]);
    }

    public function edit(Jenazah $jenazah): Response
    {
        Gate::authorize('update', $jenazah);

        return Inertia::render('jenazah/edit', [
            'jenazah' => new JenazahResource($jenazah),
            'opsi' => $this->opsi(),
            'url' => ['index' => route('jenazah.index'), 'simpan' => route('jenazah.update', $jenazah), 'batal' => route('jenazah.show', $jenazah)],
        ]);
    }

    public function update(UpdateJenazahRequest $request, Jenazah $jenazah): RedirectResponse
    {
        Gate::authorize('update', $jenazah);

        $jenazah->update($request->validated());

        return redirect()->route('jenazah.show', $jenazah)
            ->with('sukses', "Data jenazah {$jenazah->kode_jenazah} berhasil diperbarui.");
    }

    public function destroy(Jenazah $jenazah): RedirectResponse
    {
        Gate::authorize('delete', $jenazah);

        $jenazah->delete();

        return redirect()->route('jenazah.index')
            ->with('sukses', "Jenazah {$jenazah->kode_jenazah} berhasil dihapus.");
    }

    private function opsi(): array
    {
        return [
            'jenis_kelamin' => Opsi::dari(Jenazah::JENIS_KELAMIN),
            'kondisi_jenazah' => Opsi::dari(Jenazah::KONDISI),
        ];
    }

    /**
     * Data turunan di halaman rincian hanya dimuat bila pengguna berhak
     * melihatnya (Policy), jadi data yang tidak berhak tidak pernah terkirim.
     */
    private function relasiTerlihat(User $user, Jenazah $jenazah): array
    {
        $postmortem = $jenazah->dataPostmortem;

        return [
            'postmortem' => $postmortem && $user->can('view', $postmortem)
                ? new DataPostmortemResource($postmortem->load(['jenazah', 'pemeriksa'])) : null,
            'kecocokan' => $user->can('viewAny', KecocokanIdentifikasi::class)
                ? KecocokanIdentifikasiResource::collection($jenazah->kecocokan()->with(['jenazah', 'laporanKehilangan', 'pengaju', 'verifikator'])->latest()->get())
                : null,
            'dokumen' => $user->can('create', DokumenPendukung::class)
                ? DokumenPendukungResource::collection($jenazah->dokumenPendukung()->with(['jenazah', 'pengunggah'])->latest()->get())
                : null,
            'can' => [
                'tambahPostmortem' => ! $postmortem && $user->can('create', DataPostmortem::class),
                'tambahDokumen' => $user->can('create', DokumenPendukung::class),
            ],
        ];
    }
}
