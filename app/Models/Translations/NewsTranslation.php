<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsTranslation extends Model
{
    use HasFactory;
    protected $table = "news_translations";
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'date',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keyword',
    ];
}
