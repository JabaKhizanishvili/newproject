<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpcomingEventsTranslation extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "upcomin_events_translations";
    protected $fillable = [
        'locations',
        'title',
        'description'
    ];
}
