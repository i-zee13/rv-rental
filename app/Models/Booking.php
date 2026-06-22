<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['reference','user_id','customer_id','vehicle_id','pickup_location_id','return_location_id','pickup_at','return_at','start_date','end_date','pickup_location','dropoff_location','extras','subtotal','taxes','total','currency','status','notes','first_name','last_name','email','phone'];

    protected $casts = [
        'extras' => 'array'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function addons()
    {
        return $this->hasMany(BookingAddon::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'paid')->exists()
            || $this->status === 'confirmed';
    }
}
