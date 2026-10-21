<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menjaga pintu halaman: "boleh membuka kelompok rute ini?"
 * Dipakai sebagai ->middleware('peran:admin,petugas_forensik').
 * Kewenangan terhadap satu data tertentu tetap diputuskan Policy.
 */
class PastikanPeran
{
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        if (! $request->user()?->punyaPeran(...$peran)) {
            abort(403, 'Peran Anda tidak diizinkan membuka halaman ini.');
        }

        return $next($request);
    }
}
