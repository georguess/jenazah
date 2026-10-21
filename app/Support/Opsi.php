<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Mengubah daftar nilai sah (konstanta model) atau koleksi model menjadi
 * [{ value, label }] untuk elemen <select> di React. Urutan tetap terjaga.
 */
class Opsi
{
    public static function dari(array $daftar): array
    {
        return collect($daftar)
            ->map(fn (string $label, string $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }

    public static function model(Collection $model, callable $label): array
    {
        return $model
            ->map(fn ($baris) => ['value' => $baris->id, 'label' => $label($baris)])
            ->values()
            ->all();
    }
}
