<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\PenggunaResource;
use App\Models\User;
use App\Support\Opsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/** Kelola pengguna (khusus admin: Gate kelola-pengguna lewat UserPolicy). */
class UserController extends Controller
{
    private const JEJAK_AUDIT = ['jenazahDicatat', 'pemeriksaanPostmortem', 'kecocokanDiajukan', 'dokumenDiunggah'];

    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);

        $pengguna = User::withExists(self::JEJAK_AUDIT)->orderBy('name')->paginate(10);

        return Inertia::render('pengguna/index', [
            'pengguna' => PenggunaResource::collection($pengguna),
            'can' => ['tambah' => $request->user()->can('create', User::class)],
            'url' => ['index' => route('pengguna.index'), 'tambah' => route('pengguna.create')],
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', User::class);

        return Inertia::render('pengguna/create', [
            'opsi' => ['role' => Opsi::dari(User::DAFTAR_ROLE)],
            'url' => ['simpan' => route('pengguna.store'), 'batal' => route('pengguna.index')],
        ]);
    }

    /** Akun buatan admin dianggap terverifikasi agar bisa langsung masuk. */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $pengguna = User::create($request->validated());
        $pengguna->markEmailAsVerified();

        return redirect()->route('pengguna.show', $pengguna)->with('sukses', "Akun {$pengguna->name} berhasil dibuat.");
    }

    public function show(User $pengguna): Response
    {
        Gate::authorize('view', $pengguna);

        return Inertia::render('pengguna/show', [
            'pengguna' => new PenggunaResource($pengguna->loadExists(self::JEJAK_AUDIT)),
            'url' => ['index' => route('pengguna.index')],
        ]);
    }

    public function edit(User $pengguna): Response
    {
        Gate::authorize('update', $pengguna);

        return Inertia::render('pengguna/edit', [
            'pengguna' => new PenggunaResource($pengguna->loadExists(self::JEJAK_AUDIT)),
            'opsi' => ['role' => Opsi::dari(User::DAFTAR_ROLE)],
            'url' => ['simpan' => route('pengguna.update', $pengguna), 'batal' => route('pengguna.show', $pengguna)],
        ]);
    }

    public function update(UpdateUserRequest $request, User $pengguna): RedirectResponse
    {
        Gate::authorize('update', $pengguna);

        $pengguna->update(array_filter($request->validated(), fn ($nilai, $kolom) => $kolom !== 'password' || filled($nilai), ARRAY_FILTER_USE_BOTH));

        return redirect()->route('pengguna.show', $pengguna)->with('sukses', "Akun {$pengguna->name} berhasil diperbarui.");
    }

    public function destroy(User $pengguna): RedirectResponse
    {
        Gate::authorize('delete', $pengguna);

        $pengguna->delete();

        return redirect()->route('pengguna.index')->with('sukses', "Akun {$pengguna->name} berhasil dihapus.");
    }
}
