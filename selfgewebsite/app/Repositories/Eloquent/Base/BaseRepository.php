<?php


namespace App\Repositories\Eloquent\Base;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BaseRepository implements EloquentRepositoryInterface
{
    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getData($request, array $with = [])
    {
        $data = $this->model->filter($request)->with($with);

        $perPage = 10;

        if ($request->filled('per_page')) {
            $perPage = $request->per_page;
        }

        return $data->paginate($perPage);
    }

    public function all(array $columns = ["*"], array $with = [])
    {
        return $this->model->with($with)->get($columns);
    }

    public function create(array $attributes = []): Model
    {
        // try {
        return $this->model->create($attributes);

        //} catch (\Illuminate\Database\QueryException $exception) {
        //return $exception->errorInfo;
        // }
    }

    public function update(int $id, array $data = [])
    {
        $this->model = $this->findOrFail($id);
        try {
            return $this->model->update($data);
        } catch (\Illuminate\Database\QueryException $exception) {
            return $exception->errorInfo;
        }
    }

    public function delete(int $id)
    {
        $this->model = $this->findOrFail($id);
        try {
            $this->model->delete($id);
            return $this->findTrash($id);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function findOrFail(int $id, array $columns = ['*'])
    {
        $data = $this->model->find($id, $columns);
        if (!$data) {
            throw new NotFoundHttpException();
        }
        return $data;
    }

    public function findTrash(int $id): Model
    {
        $model = $this->model->withTrashed()->find($id);
        if (null === $model) {
            throw new NotFoundHttpException();
        }

        if (null === $model->deleted_at) {
            throw new NotFoundHttpException();
        }
        return $model;
    }

    public function saveFiles(int $id, $request): Model
    {
        // dd($request);
        $this->model = $this->findOrFail($id);
        // Delete old files if exist
        if (count($this->model->files)) {
            foreach ($this->model->files as $file) {
                if (!$request->old_images) {
                    $file->delete();
                    continue;
                }
                if (!in_array((string)$file->id, $request->old_images, true)) {
                    $file->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            // Get Name Of model
            $reflection = new ReflectionClass(get_class($this->model));
            $modelName = $reflection->getShortName();

            foreach ($request->file('images') as $key => $file) {
                $img = \Image::make($file);
                $imagename = date('Ymhs') . str_replace(' ', '', $file->getClientOriginalName());
                $destination = base_path() . '/storage/app/public/' . $modelName . '/' . $this->model->id;
                // $destination = base_path() . '/storage/app/public/' . $modelName . '/' . $this->model->id . '/';
                // $request->file('images')[$key]->move($destination, $imagename);
                // $img->move($destination, $imagename);
                if (!file_exists($destination)) {
                    mkdir($destination, 0777, true);
                }
                $img->save("{$destination}/{$imagename}", 20);
                $this->model->files()->create([
                    'title' => $imagename,
                    'path' => 'storage/' . $modelName . '/' . $this->model->id,
                    'format' => $file->getClientOriginalExtension(),
                    'type' => File::FILE_DEFAULT
                ]);
            }
        }

        return $this->model;
    }
}
