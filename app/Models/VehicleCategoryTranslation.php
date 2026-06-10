<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCategoryTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'vehicle_category_translations';

    protected $fillable = ['vehicle_category_id', 'locale', 'name', 'description'];

    public function category()
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }
}
