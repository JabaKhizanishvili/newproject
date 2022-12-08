<?php

namespace App\Models;

use App\Models\Translations\PageTranslation;
use App\Traits\ScopeFilter;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use Translatable, HasFactory, ScopeFilter;


    protected $table = 'pages';

    protected $fillable = [
        'key',
        'images',
        'url'
    ];

    //protected $with = 'sections';

    protected $translationModel = PageTranslation::class;

    public $translatedAttributes = [
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
            'title' => [
                'hasParam' => true,
                'scopeMethod' => 'titleTranslation'
            ]
        ];
    }


    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable')->where('type', '!=', 4);
    }

    public function docs()
    {
        return $this->morphMany(File::class, 'fileable')->where('type', 4);
    }

    public function sections()
    {
        return $this->hasMany(PageSection::class);
    }
}
