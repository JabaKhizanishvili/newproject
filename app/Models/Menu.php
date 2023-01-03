<?php

namespace App\Models;

// use App\Models\Translations\NewsTranslation;
// use App\Models\Translations\BlogTranslation as NewsTranslation;
use App\Models\Translations\MenuTranslation as NewsTranslation;
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


class Menu extends Model implements Searchable
{
    use Translatable, HasFactory, ScopeFilter;

    protected $table = 'menus';

    protected $fillable = [
        // 'slug',
        // 'status',
        'name',
        'parent_id',
        'slider',
        'sliderlike',
        'slider_order',
        'slider1',
        'slider1_order',
        'partners',
        'partners_order',
        'reports',
        'reports_order',
        'subscribers',
        'subscribers_order',
        'blog',
        'blog_order',
        'layout',
        'status',
        'show',
        'showinfooter',
    ];

    protected $translationModel = NewsTranslation::class;

    public $translatedAttributes = [
        'name',
        'visible',
        "meta_title",
        "meta_description",
        "meta_keyword",
        "meta_og_title",
        "meta_og_description",
    ];
    protected $searchableColumns = ["name", 'visible'];


    public function getFilterScopes(): array
    {
        return [
            'id' => [
                'hasParam' => true,
                'scopeMethod' => 'id'
            ],
            'name' => [
                'hasParam' => true,
                'scopeMethod' => 'name'
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

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function blog()
    {
        return $this->hasMany(Blog::class, 'category');
    }
    /**
     * @return MorphMany
     */
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

    // public function iconimages()
    // {
    //     return $this->hasMany(IconImage::class);
    // }

    public function latestImage()
    {
        return $this->morphOne(File::class, 'fileable')->latestOfMany();
    }
}
