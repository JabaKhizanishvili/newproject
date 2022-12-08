<?php
/**
 *  app/Repositories/Eloquent/LanguageRepository.php
 *
 * Date-Time: 04.06.21
 * Time: 09:55
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Repositories\Eloquent;


use App\Models\Language;
use App\Repositories\Eloquent\Base\BaseRepository;
use App\Repositories\LanguageRepositoryInterface;

/**
 * Class LanguageRepository
 * @package App\Repositories\Eloquent
 */
class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{
    /**
     * LanguageRepository constructor.
     *
     * @param \App\Models\Language $model
     */
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }
}