<?php
/**
 *  app/Repositories/LanguageRepositoryInterface.php
 *
 * Date-Time: 04.06.21
 * Time: 09:54
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Repositories;


use App\Http\Requests\Admin\LanguageRequest;

/**
 * Interface LanguageRepositoryInterface
 * @package App\Repositories
 */
interface LanguageRepositoryInterface
{

    /**
     * @param LanguageRequest $request
     * @param array $with
     */
    public function getData(LanguageRequest $request, array $with = []);
}