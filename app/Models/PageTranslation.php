<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['page_id','locale','title','content','meta_title','meta_description'];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
