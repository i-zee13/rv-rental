<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAddon extends Model
{
    protected $fillable = ['booking_id','addon_id','quantity','price'];

    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }
}
