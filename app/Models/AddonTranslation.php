<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['addon_id', 'locale', 'title', 'description'];
}
