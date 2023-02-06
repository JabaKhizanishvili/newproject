<?php
/**
 *  app/Repositories/Eloquent/Base/EloquentRepositoryInterface.php
 *
 * Date-Time: 04.06.21
 * Time: 09:41
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Repositories\Eloquent\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface EloquentRepositoryInterface
 * @package App\Repositories\Eloquent\Base
 */
interface EloquentRepositoryInterface
{

    /**
     * @param $request
     * @param array $with
     *
     * @return mixed
     */
    public function getData($request,array $with = []);

    /**
     * @param array $columns
     * @param array $with
     */
    public function all(array $columns, array $with = []);

    /**
     * @param array $attributes
     *
     */
    public function create(array $attributes = []);

    /**
     * Update model by the given ID
     *
     * @param integer $id
     * @param array $data
     *
     * @return mixed
     */
    public function update(int $id, array $data = []);

    /**
     * @param integer $id
     *
     * @return \Illuminate\Database\Eloquent\Model|string

     */
    public function delete(int $id);

    /**
     * @param integer $id
     *
     * @return Model
     */
    public function findTrash(int $id): Model;

    /**
     * @param int $id
     * @param $request
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function saveFiles(int $id,$request): Model;

}
