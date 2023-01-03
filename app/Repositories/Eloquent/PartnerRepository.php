<?php

namespace App\Repositories\Eloquent;

use App\Models\Partner;
use App\Models\Product;
use App\Repositories\Eloquent\Base\BaseRepository;
use App\Repositories\NewsRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;

class PartnerRepository extends BaseRepository implements NewsRepositoryInterface
{

    public function __construct(Partner $model)
    {
        parent::__construct($model);
    }
}
