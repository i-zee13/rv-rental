<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = ['code','type','value','starts_at','expires_at','uses','max_uses','is_active'];

    protected $casts = [
        'starts_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];
}
