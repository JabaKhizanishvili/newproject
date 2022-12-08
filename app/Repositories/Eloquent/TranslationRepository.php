<?php
/**
 *  app/Repositories/Eloquent/TranslationRepository.php
 *
 * Date-Time: 07.06.21
 * Time: 09:47
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Repositories\Eloquent;


use App\Models\LanguageLine;
use App\Repositories\Eloquent\Base\BaseRepository;
use App\Repositories\TranslationRepositoryInterface;

/**
 * Class TranslationRepository
 * @package App\Repositories\Eloquent
 */
class TranslationRepository extends BaseRepository implements TranslationRepositoryInterface
{
    /**
     * LanguageRepository constructor.
     *
     * @param \App\Models\Language $model
     */
    public function __construct(LanguageLine $model)
    {
        parent::__construct($model);
    }
}