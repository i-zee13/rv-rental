<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiLog extends Model
{
    use HasFactory;

    protected $fillable = ['action','prompt','response','meta'];

    protected $casts = [
        'meta' => 'array',
    ];
}
