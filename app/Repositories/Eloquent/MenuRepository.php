<?php

/**
 *  app/Repositories/Eloquent/ProductRepository.php
 *
 * Date-Time: 30.07.21
 * Time: 10:36
 * @author Insite.ge
 */

namespace App\Repositories\Eloquent;


use App\Models\Menu;
use App\Models\Product;
use App\Repositories\Eloquent\Base\BaseRepository;
use App\Repositories\NewsRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;

/**
 * Class LanguageRepository
 * @package App\Repositories\Eloquent
 */
class MenuRepository extends BaseRepository implements NewsRepositoryInterface
{

    public function __construct(Menu $model)
    {
        parent::__construct($model);
    }
}
