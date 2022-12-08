<?php
/**
 *  app/Repositories/Eloquent/ProductRepository.php
 *
 * Date-Time: 30.07.21
 * Time: 10:36
 * @author Insite.ge
 */

namespace App\Repositories\Eloquent;


use App\Models\News;
use App\Models\Product;
use App\Repositories\Eloquent\Base\BaseRepository;
use App\Repositories\NewsRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;

/**
 * Class LanguageRepository
 * @package App\Repositories\Eloquent
 */
class NewsRepository extends BaseRepository implements NewsRepositoryInterface
{
    /**
     * @param \App\Models\Product $model
     */
    public function __construct(News $model)
    {
        parent::__construct($model);
    }

}
