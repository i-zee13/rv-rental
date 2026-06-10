<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTemplateTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['email_template_id','locale','subject','body'];

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }
}
