<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class SettingTranslation extends BaseTranslationModel
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'value'
    ];
}
