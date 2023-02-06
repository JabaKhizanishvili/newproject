<?php

namespace App\Models;

// use App\Models\Translations\NewsTranslation;
use App\Models\Translations\BlogTranslation as NewsTranslation;
use App\Models\Translations\ProductTranslation;
use App\Traits\ScopeFilter;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;


class Blog extends Model implements Searchable
{
    use Translatable, HasFactory, ScopeFilter;

    protected $table = 'blogs';

    protected $fillable = [
        'category',
        'status',
        'visible',
        'publish_start',
        'publish_end',
        'vaccancylink',
        'vaccancylink1',
        'vaccancylink2',
        'vaccancylink3',
        'btncolor',
    ];

    protected $translationModel = NewsTranslation::class;

    public $translatedAttributes = [
        'title',
        'description',
        'show',
        'icons',
        'button_text',
        'icon1_text',
        'icon2_text',
        'icon3_text',
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

    public function getSearchResult(): SearchResult
    {
        $url = locale_route('client.product.show', $this->slug);

        return new SearchResult(
            $this,
            $this->title,
            $url
        );
    }

    public function iconimages()
    {
        return $this->hasMany(Iconimage::class, 'blog_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function menu()
    {
        return $this->hasOne(Menu::class, 'category');
    }

    public function latestImage()
    {
        return $this->morphOne(File::class, 'fileable')->latestOfMany();
    }
}
