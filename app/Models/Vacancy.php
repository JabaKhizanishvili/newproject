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
use App\Models\Translations\VacancyTranslation;
use App\Traits\ScopeFilter;
use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;


/**
 * App\Models\Category
 *
 * @property int $id
 * @property int|null $category_id
 * @property string|null $slug
 * @property string|null $position
 * @property int|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read CategoryTranslation|null $translation
 * @property-read \Illuminate\Database\Eloquent\Collection|CategoryTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Category listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Query\Builder|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Category orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Category orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|Category translated()
 * @method static \Illuminate\Database\Eloquent\Builder|Category translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category withTranslation()
 * @method static \Illuminate\Database\Query\Builder|Category withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Category withoutTrashed()
 */
class Vacancy extends Model
{
    use Translatable, HasFactory, ScopeFilter;


    /**
     * @var string
     */
    protected $table = 'vacancies';

    /**
     * @var string[]
     */
    protected $fillable = [
        'slug',
        'position',
        'status',
        'rate'
    ];

    /** @var string */
    protected $translationModel = VacancyTranslation::class;

    /** @var array */
    public $translatedAttributes = [
        'title',
        'sub_title',
        'description',
        'remuneration',
        'time'
    ];


    protected $with = ['translations'];



    //    public function scopeFilter($query, array $filters)
    //    {
    ////        $category = $filters["category"];
    //        $query->when($filters["category"] ?? false, function () use ($query) {
    ////            dd($category->get());
    ////            dd($query);
    //            return $query->where("id", $filters["category"])->get();
    ////            return $query->whereHas("category", function () use ($query, $category) {
    ////                return $query->where("slug", $category);
    ////            }
    ////            );
    //        }
    //        );
    //
    //
    //    }

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
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'skill_vacancy');
    }


    /**
     * @return MorphMany
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable')->where('type','!=',4);
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

    public function resumes():HasMany{
        return $this->hasMany(Resume::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return (new Carbon($value))->format('d/m/Y');
    }
}
