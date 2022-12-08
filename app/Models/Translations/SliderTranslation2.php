<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderTranslation2 extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'title_2',
        'short_description',
        'description',
    ];
}
