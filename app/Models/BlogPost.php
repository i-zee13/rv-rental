<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['slug', 'status', 'author_id', 'featured_image'];

    public function translations()
    {
        return $this->hasMany(BlogPostTranslation::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
