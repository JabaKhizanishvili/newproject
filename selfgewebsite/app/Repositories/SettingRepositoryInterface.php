<?php
/**
 *  app/Repositories/SettingRepositoryInterface.php
 */
namespace App\Repositories;


use App\Http\Requests\Admin\SettingRequest;


interface SettingRepositoryInterface
{

    /**
     * @param SettingRequest $request
     * @param array $with
     *
     * @return mixed
     */
    public function getData(SettingRequest $request, array $with = []);
}
