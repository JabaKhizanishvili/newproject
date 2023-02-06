<?php

/**
 *  app/Models/Language.php
 *
 * Date-Time: 08.06.21
 * Time: 14:16
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Models;

use App\Traits\ScopeFilter;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Language
 * @package App\Models
 * @property integer $id
 * @property string $title
 * @property string $locale
 * @property boolean $default
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Language extends Model
{
    use HasFactory,  ScopeFilter, FormAccessible;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'languages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'locale', 'status'];


    public function getFilterScopes(): array
    {
        return [
            'id' => [
                'hasParam' => true,
                'scopeMethod' => 'id'
            ],
            'title' => [
                'hasParam' => true,
                'scopeMethod' => 'title'
            ],
            'locale' => [
                'hasParam' => true,
                'scopeMethod' => 'locale'
            ],
            'status' => [
                'hasParam' => true,
                'scopeMethod' => 'status'
            ]
        ];
    }
}
