<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageTranslation extends BaseTranslationModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_2',
        'description',
        'description_2',
        "meta_title",
        "meta_description",
        "meta_keyword",
        "meta_og_title",
        "meta_og_description",
    ];
}
