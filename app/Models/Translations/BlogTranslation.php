<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogTranslation extends Model
{
    use HasFactory;
    protected $table = "blogs_translations";
    public $timestamps = false;

    protected $fillable = [
        'title',
        'icons',
        'description',
        'visible',
        'button_text',
    ];
}
