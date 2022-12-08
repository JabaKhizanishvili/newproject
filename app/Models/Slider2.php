<?php

namespace App\Models;

use App\Models\Translations\SliderTranslation2;
use App\Traits\ScopeFilter;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider2 extends Model
{
    use SoftDeletes, Translatable, HasFactory, ScopeFilter;

    protected $table = 'slider2s';
    protected $fillable = [
        'status',
        'logo',
        // 'youtube_url',
        'reddirect_url'
    ];


    protected $translationModel = SliderTranslation2::class;

    /** @var array */
    public $translatedAttributes = [
        'title',
        // 'title_2',
        'description',
        'short_description',
    ];

    public function getFilterScopes(): array
    {
        return [
            'id' => [
                'hasParam' => true,
                'scopeMethod' => 'id'
            ],
            'status' => [
                'hasParam' => true,
                'scopeMethod' => 'status'
            ],
            'title' => [
                'hasParam' => true,
                'scopeMethod' => 'titleTranslation'
            ]
        ];
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }
    /**
     * @return MorphOne
     */
    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
