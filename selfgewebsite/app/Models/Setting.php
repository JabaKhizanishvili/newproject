<?php

namespace App\Models;

use App\Models\Translations\SettingTranslation;
use App\Traits\ScopeFilter;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes, Translatable, HasFactory, ScopeFilter;


    protected $table = 'settings';

    protected $fillable = [
        'key'
    ];

    protected $translationModel = SettingTranslation::class;

    public $translatedAttributes = [
        'value'
    ];


    public function getFilterScopes(): array
    {
        return [
            'id' => [
                'hasParam' => true,
                'scopeMethod' => 'id'
            ],
            'key' => [
                'hasParam' => true,
                'scopeMethod' => 'key'
            ],
            'value' => [
                'hasParam' => true,
                'scopeMethod' => 'valueTranslation'
            ]
        ];
    }


}
