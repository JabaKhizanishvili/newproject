<?php

namespace App\Models;

use App\Models\Translations\ProductTranslation;
use App\Models\Translations\SubclassTranslation;
use App\Traits\ScopeFilter;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;




class Subclass extends Model
{
    use Translatable, HasFactory, ScopeFilter;

    /**
     * @var string
     */
    protected $table = 'subclasses';

    /**
     * @var string[]
     */
    protected $fillable = [
        'customer_id',
        'class_id',
        'status'
    ];

    /** @var string */
    protected $translationModel = SubclassTranslation::class;

    //protected $with = ['translation'];

    /** @var array */
    public $translatedAttributes = [
        'title',
    ];

    //protected $with = ['translation'];


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
            ],
            'category_id' => [
                'hasParam' => true,
                'scopeMethod' => 'categoryId'
            ]
        ];
    }



    /**
     * @return BelongsTo
     */
    /*public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }*/

    /**
     * The categories that belong to the product.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class);
    }

    public function customer():BelongsTo{
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return MorphMany
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable')->where('type','!=',4);
    }

    public function latestImage()
    {
        return $this->morphOne(File::class, 'fileable')->latestOfMany();
    }


    /**
     * @return MorphOne
     */
    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function docs(){
        return $this->morphMany(File::class, 'fileable')->where('type',4);
    }

}
