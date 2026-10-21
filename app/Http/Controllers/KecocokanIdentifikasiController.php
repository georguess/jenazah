<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKecocokanIdentifikasiRequest;
use App\Http\Requests\UpdateKecocokanIdentifikasiRequest;
use App\Http\Requests\VerifikasiKecocokanRequest;
use App\Http\Resources\DataAntemortemResource;
use App\Http\Resources\DataPostmortemResource;
use App\Http\Resources\KecocokanIdentifikasiResource;
use App\Models\Jenazah;
use App\Models\KecocokanIdentifikasi;
use App\Models\LaporanKehilangan;
use App\Support\Opsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class KecocokanIdentifikasiController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', KecocokanIdentifikasi::class);

        $kecocokan = KecocokanIdentifikasi::with(['jenazah', 'laporanKehilangan', 'pengaju', 'verifikator'])
            ->when($request->query('status'), fn ($q, $status) => $q->where('status_verifikasi', $status))
            ->latest()->paginate(10)->withQueryString();

        return Inertia::render('kecocokan/index', [
            'kecocokan' => KecocokanIdentifikasiResource::collection($kecocokan),
            'filter' => ['status' => $request->query('status', '')],
            'opsi' => ['status' => Opsi::dari(KecocokanIdentifikasi::STATUS_VERIFIKASI)],
            'can' => ['tambah' => $request->user()->can('create', KecocokanIdentifikasi::class)],
            'url' => ['index' => route('kecocokan.index'), 'tambah' => route('kecocokan.create')],
        ]);
    }

    public function create(Request $request): Response
    {
        Gate::authorize('create', KecocokanIdentifikasi::class);

        return Inertia::render('kecocokan/create', [
            'opsi' => $this->opsiPengajuan(),
            'bawaan' => ['jenazah_id' => $request->integer('jenazah_id') ?: ''],
            'url' => ['simpan' => route('kecocokan.store'), 'batal' => route('kecocokan.index')],
        ]);
    }

    public function store(StoreKecocokanIdentifikasiRequest $request): RedirectResponse
    {
        Gate::authorize('create', KecocokanIdentifikasi::class);

        $kecocokan = KecocokanIdentifikasi::ajukan($request->validated(), $request->user());

        return redirect()->route('kecocokan.show', $kecocokan)
            ->with('sukses', 'Usulan kecocokan berhasil diajukan dan menunggu verifikasi admin.');
    }

    /** Rincian menampilkan antemortem dan postmortem berdampingan untuk dibandingkan. */
    public function show(KecocokanIdentifikasi $kecocokan): Response
    {
        Gate::authorize('view', $kecocokan);

        $kecocokan->load(['jenazah.dataPostmortem.jenazah', 'jenazah.dataPostmortem.pemeriksa', 'laporanKehilangan.dataAntemortem.laporanKehilangan', 'pengaju', 'verifikator']);
        $postmortem = $kecocokan->jenazah->dataPostmortem;
        $antemortem = $kecocokan->laporanKehilangan->dataAntemortem;

        return Inertia::render('kecocokan/show', [
            'kecocokan' => new KecocokanIdentifikasiResource($kecocokan),
            'postmortem' => $postmortem ? new DataPostmortemResource($postmortem) : null,
            'antemortem' => $antemortem ? new DataAntemortemResource($antemortem) : null,
            'opsi' => ['status' => Opsi::dari(array_diff_key(KecocokanIdentifikasi::STATUS_VERIFIKASI, [KecocokanIdentifikasi::STATUS_MENUNGGU => 1]))],
            'url' => ['index' => route('kecocokan.index')],
        ]);
    }

    public function edit(KecocokanIdentifikasi $kecocokan): Response
    {
        Gate::authorize('update', $kecocokan);

        return Inertia::render('kecocokan/edit', [
            'kecocokan' => new KecocokanIdentifikasiResource($kecocokan->load(['jenazah', 'laporanKehilangan'])),
            'opsi' => ['metode' => Opsi::dari(KecocokanIdentifikasi::METODE)],
            'url' => ['simpan' => route('kecocokan.update', $kecocokan), 'batal' => route('kecocokan.show', $kecocokan)],
        ]);
    }

    public function update(UpdateKecocokanIdentifikasiRequest $request, KecocokanIdentifikasi $kecocokan): RedirectResponse
    {
        Gate::authorize('update', $kecocokan);

        $kecocokan->update($request->validated());

        return redirect()->route('kecocokan.show', $kecocokan)
            ->with('sukses', 'Usulan kecocokan berhasil diperbarui.');
    }

    public function destroy(KecocokanIdentifikasi $kecocokan): RedirectResponse
    {
        Gate::authorize('delete', $kecocokan);

        $kecocokan->delete();

        return redirect()->route('kecocokan.index')
            ->with('sukses', 'Usulan kecocokan berhasil dihapus.');
    }

    /** Pilihan formulir: jenazah yang belum teridentifikasi dan laporan yang masih aktif. */
    private function opsiPengajuan(): array
    {
        $jenazah = Jenazah::where('status_identifikasi', '!=', Jenazah::STATUS_TERIDENTIFIKASI)->orderBy('kode_jenazah')->get(['id', 'kode_jenazah', 'lokasi_ditemukan']);
        $laporan = LaporanKehilangan::where('status', LaporanKehilangan::STATUS_AKTIF)->orderBy('nama_korban_hilang')->get(['id', 'nama_korban_hilang', 'lokasi_terakhir']);

        return [
            'metode' => Opsi::dari(KecocokanIdentifikasi::METODE),
            'jenazah' => Opsi::model($jenazah, fn ($j) => "{$j->kode_jenazah} — {$j->lokasi_ditemukan}"),
            'laporan' => Opsi::model($laporan, fn ($l) => "{$l->nama_korban_hilang} — {$l->lokasi_terakhir}"),
        ];
    }

    /** Verifikasi akhir (khusus admin): menyetujui atau menolak usulan. */
    public function verifikasi(VerifikasiKecocokanRequest $request, KecocokanIdentifikasi $kecocokan): RedirectResponse
    {
        Gate::authorize('verifikasi', $kecocokan);

        $kecocokan->verifikasi($request->validated('status_verifikasi'), $request->validated('catatan_verifikasi'), $request->user());

        return redirect()->route('kecocokan.show', $kecocokan)
            ->with('sukses', 'Kecocokan telah '.mb_strtolower(KecocokanIdentifikasi::STATUS_VERIFIKASI[$kecocokan->status_verifikasi]).'.');
    }
}
