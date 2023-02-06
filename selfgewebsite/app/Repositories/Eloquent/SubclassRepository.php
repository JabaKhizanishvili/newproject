<?php
/**
 *  app/Repositories/Eloquent/ProductRepository.php
 *
 * Date-Time: 30.07.21
 * Time: 10:36
 * @author Vakho Batsikadze <vakhobatsikadze@gmail.com>
 */

namespace App\Repositories\Eloquent;


use App\Models\Classes;
use App\Models\File;
use App\Models\Page;
use App\Models\Subclass;
use App\Models\Vacancy;
use App\Repositories\Eloquent\Base\BaseRepository;
use App\Repositories\PageRepositoryInterface;
use App\Repositories\VacancyRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;


class SubclassRepository extends BaseRepository
{
    /**
     * @param Page $model
     */
    public function __construct(Subclass $model)
    {
        parent::__construct($model);
    }


    public function saveFilesDocs(int $id, $request): Model
    {
        $this->model = $this->findOrFail($id);

        if ($request->hasFile('files')) {
            //dd($request->file('files'));
            // Get Name Of model
            $reflection = new ReflectionClass(get_class($this->model));
            $modelName = $reflection->getShortName();

            foreach ($request->file('files') as $key => $file) {

                $imagename = date('Ymhs') . str_replace(' ', '', $file->getClientOriginalName());
                $destination = base_path() . '/storage/app/public/' . $modelName . '/' . $this->model->id .'/files';
                $request->file('files')[$key]->move($destination, $imagename);
                $this->model->files()->create([
                    'title' => $imagename,
                    'path' => 'storage/' . $modelName . '/' . $this->model->id . '/files',
                    'format' => $file->getClientOriginalExtension(),
                    'type' => File::FILE_DOC
                ]);
            }
        }

        return $this->model;
    }
}
