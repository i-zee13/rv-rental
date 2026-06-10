<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['vehicle_id','locale','title','description','specs','meta_title','meta_description','meta_keywords'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
