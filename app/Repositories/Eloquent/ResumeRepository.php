<?php
/**
 *  app/Repositories/Eloquent/ProductRepository.php
 *
 * Date-Time: 30.07.21
 * Time: 10:36
 * @author Vakho Batsikadze <vakhobatsikadze@gmail.com>
 */

namespace App\Repositories\Eloquent;


use App\Models\File;
use App\Models\Page;
use App\Models\Resume;
use App\Models\Vacancy;
use App\Repositories\Eloquent\Base\BaseRepository;
use App\Repositories\PageRepositoryInterface;
use App\Repositories\ResumeRepositoryInterface;
use App\Repositories\VacancyRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;


class ResumeRepository extends BaseRepository implements ResumeRepositoryInterface
{
    /**
     * @param Page $model
     */
    public function __construct(Resume $model)
    {
        parent::__construct($model);
    }


    public function saveResume($request): Model
    {
        //dd($request->all());
        $vacancy = Vacancy::query()->findOrFail($request->vacancy_id);

        if ($request->hasFile('doc')) {

            // Get Name Of model

            $imagename = date('Y_m_d_H_i_s_') . str_replace(' ', '', $request->file('doc')->getClientOriginalName());
            $destination = base_path() . '/storage/app/public/Vacancy/' . $vacancy->id .'/resumes';
            $request->file('doc')->move($destination, $imagename);
            //dd($destination,$vacancy);
            $vacancy->resumes()->create([
                'file_name' => $imagename,
                'path' => 'storage/Vacancy/' . $vacancy->id . '/resumes',
            ]);

        }

        return $this->model;
    }
}
