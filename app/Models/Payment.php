<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id','provider','provider_id','amount','currency','status','meta'];

    protected $casts = [
        'meta' => 'array'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
