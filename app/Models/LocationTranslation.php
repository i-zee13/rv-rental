<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LocationTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['location_id','locale','title','description'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
