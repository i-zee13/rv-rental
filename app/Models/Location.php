<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['type','code','address','city','state','country','latitude','longitude','phone','email','opening_hours','is_active'];

    protected $casts = [
        'opening_hours' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function translations()
    {
        return $this->hasMany(LocationTranslation::class);
    }
}
