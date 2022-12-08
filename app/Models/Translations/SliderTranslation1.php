<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderTranslation1 extends BaseTranslationModel
{
    use HasFactory;
    protected $fillable = [
        'title',
        'title_2',
        'short_description',
        'description',
    ];
}
