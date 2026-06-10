<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogPostTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['blog_post_id','locale','title','excerpt','content','meta_title','meta_description'];

    public function post()
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
