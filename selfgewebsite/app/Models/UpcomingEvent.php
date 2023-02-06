<?php

namespace App\Models;

// use App\Models\Translations\SliderTranslation;
use App\Models\Translations\UpcomingEventsTranslation as SliderTranslation;
// use App\Models\Translations\UpcomingEventsTranslation;
use App\Traits\ScopeFilter;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class UpcomingEvent extends Model
{
    use Translatable, HasFactory, ScopeFilter;
    protected $table = 'upcoming_events';
    protected $fillable = [
        'status',
        'reddirect_url'
    ];

    protected $translationModel = SliderTranslation::class;

    /** @var array */
    public $translatedAttributes = [
        'locations',
        'title',
        'description'
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
