<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Addon extends Model
{
    use SoftDeletes;

    protected $fillable = ['code','price','is_taxable','is_active'];

    public function translations()
    {
        return $this->hasMany(AddonTranslation::class);
    }

    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->first();
    }

    public function getNameAttribute(): string
    {
        return $this->translation()?->title ?? $this->code;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->translation()?->description;
    }
}
