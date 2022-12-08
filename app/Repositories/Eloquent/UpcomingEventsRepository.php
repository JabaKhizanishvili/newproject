<?php

/**
 *  app/Repositories/Eloquent/ProductRepository.php
 *
 * Date-Time: 30.07.21
 * Time: 10:36
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Repositories\Eloquent;


use App\Models\Product;
// use App\Models\Slider;
use App\Models\UpcomingEvent as Slider;
use App\Repositories\Eloquent\Base\BaseRepository;
// use App\Repositories\SliderRepositoryInterface;
use App\Repositories\UpcomingEventsRepositoryInterface as SliderRepositoryInterface;

/**
 * Class LanguageRepository
 * @package App\Repositories\Eloquent
 */
class UpcomingEventsRepository extends BaseRepository implements SliderRepositoryInterface
{
    /**
     * @param \App\Models\Slider $model
     */
    public function __construct(Slider $model)
    {
        parent::__construct($model);
    }
}
