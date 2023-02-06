<?php

/**
 *  app/Models/Category.php
 *
 * Date-Time: 30.07.21
 * Time: 10:32
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Models;

use App\Models\Translations\CategoryTranslation;
use App\Models\Translations\ClassTranslation;
use App\Traits\ScopeFilter;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;



class Classes extends Model
{
    use Translatable, HasFactory, ScopeFilter;


    /**
     * @var string
     */
    protected $table = 'classes';

    /**
     * @var string[]
     */
    protected $fillable = [
        'status',
    ];

    /** @var string */
    protected $translationModel = ClassTranslation::class;

    /** @var array */
    public $translatedAttributes = [
        'title',

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
    public function subclasses(): HasMany
    {
        return $this->hasMany(Subclass::class);
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
}
