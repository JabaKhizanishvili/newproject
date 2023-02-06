<?php

namespace App\Models;

use Kalnoy\Nestedset\NodeTrait;
use App\Models\Translations\StaffTranslation;
use App\Models\Translations\PortfolioTranslation;
use App\Traits\ScopeFilter;
use App\Models\File;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    use Translatable, HasFactory, ScopeFilter;
    protected $table = 'portfolio';
    protected $fillable = ['parent_id', 'color', 'status'];

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


    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public $translatedAttributes = [
        'title',
        'description',
        'visible',
    ];

    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
    // this adds image
    public function latestImage()
    {
        return $this->morphOne(File::class, 'fileable')->latestOfMany();
    }

    protected $translationModel = PortfolioTranslation::class;
}
