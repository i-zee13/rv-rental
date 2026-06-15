<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PropertyTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'property_id', 'locale', 'title', 'description', 'highlights',
        'meta_title', 'meta_description',
    ];

    public function setMetaDescriptionAttribute(?string $value): void
    {
        $this->attributes['meta_description'] = $value
            ? Str::limit(trim(strip_tags($value)), 320, '')
            : null;
    }

    public function setMetaTitleAttribute(?string $value): void
    {
        $this->attributes['meta_title'] = $value
            ? Str::limit(trim($value), 255, '')
            : null;
    }
}
