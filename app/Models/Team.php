<?php

namespace App\Models;

use Kalnoy\Nestedset\NodeTrait;
use App\Models\Translations\StaffTranslation;
use App\Models\Translations\TeamTranslation;
use App\Traits\ScopeFilter;
use App\Models\File;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use Translatable, HasFactory, ScopeFilter;
    protected $table = 'teams';
    protected $fillable = [
        'status',
        'price',
        'special_price',
        'video_url',
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
        // 'name',
        // 'position',
        // 'slug',
        'title',
        'short_description',
        'description',
        'whattolearn',
        'course_includes',
        'starts',
    ];

    // this adds image
    public function latestImage()
    {
        return $this->morphOne(File::class, 'fileable')->latestOfMany();
    }

    protected $translationModel = TeamTranslation::class;
}
