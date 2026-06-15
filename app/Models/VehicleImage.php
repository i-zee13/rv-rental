<?php

namespace App\Models;

use App\Support\PublicMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleImage extends Model
{
    use HasFactory;

    protected $fillable = ['vehicle_id', 'path', 'alt_text', 'sort_order'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function publicUrl(): string
    {
        return PublicMedia::url($this->path ?? '');
    }
}
