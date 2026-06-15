<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyType extends Model
{
    use SoftDeletes;

    protected $fillable = ['slug', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(PropertyTypeTranslation::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function translatedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $t = $this->translations->firstWhere('locale', $locale) ?? $this->translations->first();

        return $t?->name ?? ucfirst(str_replace('-', ' ', $this->slug));
    }
}
