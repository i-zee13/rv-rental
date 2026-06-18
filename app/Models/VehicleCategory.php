<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['slug','is_active'];

    public function translations()
    {
        return $this->hasMany(VehicleCategoryTranslation::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'category_id');
    }

    public function translatedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $t = $this->translations->firstWhere('locale', $locale) ?? $this->translations->first();

        return $t?->name ?? ucfirst(str_replace('-', ' ', $this->slug));
    }
}
