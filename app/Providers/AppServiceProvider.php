<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\ExceptionResponse;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureGates();
        $this->configureErrorPages();

        // Resource tunggal dikirim sebagai objek polos (tanpa bungkus "data");
        // resource berpaginasi tetap berbentuk { data, links, meta }.
        JsonResource::withoutWrapping();
    }

    /**
     * Halaman galat yang ramah (403/404/500/503) dirender lewat Inertia
     * lengkap dengan props bersama, sehingga tata letak dan menu tetap tampil.
     * Galat 500 saat mode debug dibiarkan agar jejak galat terlihat.
     */
    protected function configureErrorPages(): void
    {
        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            $status = $response->statusCode();

            if ($status === 419) {
                return back()->with('galat', 'Sesi Anda kedaluwarsa. Silakan ulangi.');
            }

            if (! in_array($status, [403, 404, 500, 503]) || ($status >= 500 && config('app.debug'))) {
                return null;
            }

            return $response->render('galat', ['status' => $status])->withSharedData();
        });
    }

    /**
     * Gate untuk kewenangan yang TIDAK terikat pada satu baris data.
     * Kewenangan berobjek ada di app/Policies (satu Policy per model,
     * ditemukan otomatis oleh Laravel dari nama kelasnya).
     */
    protected function configureGates(): void
    {
        Gate::define('kelola-pengguna', fn (User $user) => $user->punyaPeran(User::ROLE_ADMIN));

        Gate::define('melihat-statistik', fn (User $user) => $user->punyaPeran(User::ROLE_ADMIN));
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        // Pertemuan 3: cegah N+1. Di luar produksi, relasi yang lupa dimuat
        // dengan with()/load() langsung menjadi galat, bukan kueri diam-diam.
        Model::preventLazyLoading(! app()->isProduction());

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
