<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class TimeOnlyCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        // Simpan null kalau kosong
        return $value ? substr($value, 0, 5) : null; // H:i
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value ?: null;
    }
}
