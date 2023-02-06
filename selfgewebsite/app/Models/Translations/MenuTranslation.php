<?php

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuTranslation extends Model
{
    use HasFactory;
    protected $table = "menutranslations";
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'visible',
        "meta_title",
        "meta_description",
        "meta_keyword",
        "meta_og_title",
        "meta_og_description",
    ];
}
