<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use SoftDeletes;

    public const AMENITY_OPTIONS = [
        'air_conditioning' => 'Air Conditioning',
        'in_unit_laundry' => 'In-Unit Laundry',
        'parking' => 'Parking',
        'pool' => 'Pool',
        'fitness_center' => 'Fitness Center',
        'balcony' => 'Balcony / Patio',
        'dishwasher' => 'Dishwasher',
        'hardwood_floors' => 'Hardwood Floors',
        'utilities_included' => 'Utilities Included',
        'elevator' => 'Elevator',
        'gated_community' => 'Gated Community',
        'yard' => 'Yard',
        'wheelchair_accessible' => 'Wheelchair Accessible',
    ];

    protected $fillable = [
        'property_type_id', 'reference', 'address_line1', 'address_line2',
        'city', 'state', 'zip', 'neighborhood', 'latitude', 'longitude',
        'bedrooms', 'bathrooms', 'sqft', 'max_guests', 'min_nights',
        'price_per_month', 'price_per_week', 'price_per_night',
        'security_deposit', 'cleaning_fee', 'featured', 'instant_book',
        'pets_allowed', 'furnished', 'amenities', 'available_from', 'status',
    ];

    protected $casts = [
        'bedrooms' => 'integer',
        'bathrooms' => 'float',
        'sqft' => 'integer',
        'max_guests' => 'integer',
        'min_nights' => 'integer',
        'price_per_month' => 'float',
        'price_per_week' => 'float',
        'price_per_night' => 'float',
        'security_deposit' => 'float',
        'cleaning_fee' => 'float',
        'featured' => 'boolean',
        'instant_book' => 'boolean',
        'pets_allowed' => 'boolean',
        'furnished' => 'boolean',
        'amenities' => 'array',
        'available_from' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function type()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function translations()
    {
        return $this->hasMany(PropertyTranslation::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function translation(?string $locale = null): ?PropertyTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations->firstWhere('locale', $locale) ?? $this->translations->first();
    }

    public function title(?string $locale = null): string
    {
        $t = $this->translation($locale);

        return $t?->title ?? trim($this->address_line1 . ', ' . $this->city);
    }

    public function fullAddress(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->city,
            $this->state . ' ' . $this->zip,
        ]);

        return implode(', ', $parts);
    }

    public function displayPrice(): string
    {
        if ($this->price_per_month > 0) {
            return '$' . number_format($this->price_per_month, 0) . '/mo';
        }
        if ($this->price_per_week > 0) {
            return '$' . number_format($this->price_per_week, 0) . '/wk';
        }
        if ($this->price_per_night > 0) {
            return '$' . number_format($this->price_per_night, 0) . '/night';
        }

        return 'Contact for price';
    }

    public function amenityLabels(): array
    {
        $keys = $this->amenities ?? [];

        return collect($keys)
            ->map(fn ($key) => self::AMENITY_OPTIONS[$key] ?? ucfirst(str_replace('_', ' ', $key)))
            ->filter()
            ->values()
            ->all();
    }
}
