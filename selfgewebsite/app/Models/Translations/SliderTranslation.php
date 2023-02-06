<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderTranslation extends BaseTranslationModel
{
    use HasFactory;
    protected $fillable = [
        'title',
        'title_2',
        'visible',
        'short_description',
        'description',
        'button_text',
    ];
}
