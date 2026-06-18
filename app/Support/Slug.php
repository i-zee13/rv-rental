<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Slug
{
    public static function unique(string $base, string $modelClass, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'item';
        $candidate = $slug;
        $i = 1;

        while (static::exists($modelClass, $candidate, $ignoreId)) {
            $candidate = $slug.'-'.$i;
            $i++;
        }

        return $candidate;
    }

    protected static function exists(string $modelClass, string $slug, ?int $ignoreId): bool
    {
        /** @var Model $model */
        $query = $modelClass::where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
