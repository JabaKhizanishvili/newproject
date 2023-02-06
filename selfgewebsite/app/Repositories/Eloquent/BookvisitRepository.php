<?php

/**
 *  app/Repositories/Eloquent/ProductRepository.php
 *
 * Date-Time: 30.07.21
 * Time: 10:36
 * @author Vakho Batsikadze <vakhobatsikadze@gmail.com>
 */

namespace App\Repositories\Eloquent;


use App\Models\Page;
use App\Models\Skill;
use App\Models\Bookvisit;
use App\Models\Vacancy;
use App\Repositories\Eloquent\Base\BaseRepository;
use App\Repositories\PageRepositoryInterface;
use App\Repositories\SkillRepositoryInterface;
use App\Repositories\VacancyRepositoryInterface;


class BookvisitRepository extends BaseRepository implements SkillRepositoryInterface
{
    /**
     * @param Page $model
     */
    public function __construct(Bookvisit $model)
    {
        parent::__construct($model);
    }
}
