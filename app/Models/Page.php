<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['slug','is_published'];

    public function translations()
    {
        return $this->hasMany(PageTranslation::class);
    }
}
