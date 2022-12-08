<?php
/**
 *  app/Models/LanguageLine.php
 *
 * Date-Time: 07.06.21
 * Time: 09:43
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Models;

use App\Traits\ScopeFilter;

/**
 * Class LanguageLine
 * @package App\Models
 * @property integer $id
 * @property string $group
 * @property string $key
 * @property string $text
 * @property string $created_at
 * @property string $updated_at
 */
class LanguageLine extends \Spatie\TranslationLoader\LanguageLine
{
    use ScopeFilter;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'language_lines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group', 'key', 'text'];

    public function getFilterScopes(): array
    {
        return [
            'id' => [
                'hasParam' => true,
                'scopeMethod' => 'id'
            ],
            'group' => [
                'hasParam' => true,
                'scopeMethod' => 'group'
            ],
            'key' => [
                'hasParam' => true,
                'scopeMethod' => 'key'
            ],
            'text' => [
                'hasParam' => true,
                'scopeMethod' => 'text'
            ]
        ];
    }

    /**
     * @param $query
     * @param $group
     *
     * @return mixed
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', 'like', '%' . $group . '%');
    }

    /**
     * @param $query
     * @param $key
     *
     * @return mixed
     */
    public function scopeKey($query, $key)
    {
        return $query->where('key', 'like', '%' . $key . '%');
    }

    protected function asJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

}
