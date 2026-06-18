<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'slug', 'make', 'model', 'year', 'vin', 'internal_id', 'seats', 'doors', 'bags', 'transmission', 'fuel_type', 'price_per_day', 'price_per_week', 'price_per_month', 'security_deposit', 'cleaning_fee', 'delivery_fee', 'featured', 'instant_book', 'delivery_available', 'pet_friendly', 'smoking_allowed', 'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }

    public function translations()
    {
        return $this->hasMany(VehicleTranslation::class);
    }

    public function images()
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
