<?php

/**
 *  app/Http/Controllers/Admin/ProductController.php
 *
 * Date-Time: 30.07.21
 * Time: 10:37
 * @author Insite.ge
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Blog as News;
use App\Models\Product;
use App\Models\Menu;
use App\Models\File;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\Eloquent\BlogRepository as NewsRepository;
use App\Repositories\NewsRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use ReflectionException;
use ReflectionClass;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{

    /**
     * @var NewsRepositoryInterface
     */
    private $newsRepository;


    /**
     * @param NewsRepositoryInterface $newsRepository
     */
    public function __construct(
        NewsRepository $newsRepository
    ) {
        $this->newsRepository = $newsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(NewsRequest $request)
    {
        return view('admin.nowa.views.blogs.index', [
            'news' => $this->newsRepository->getData($request, ['translations']),
            'data' => $this->newsRepository->getData($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $news = $this->newsRepository->model;
        $menu = Menu::all();
        $url = locale_route('blog.store', [], false);
        $method = 'POST';

        return view('admin.nowa.views.blogs.form1', [
            'news' => $news,
            'menus' => $menu,
            'newss' => null,
            'url' => $url,
            'method' => $method,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     *
     * @return Application|RedirectResponse|Redirector
     * @throws ReflectionException
     */
    public function store(NewsRequest $request)
    {
        $request->validate([
            // 'title' => 'required',
            "category" => 'required',
        ]);
        // dd($request->all());
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        $saveData['visible'] = isset($saveData['visible']) && (bool)$saveData['visible'];
        $news = $this->newsRepository->create($saveData);


        // Save Files
        if ($request->hasFile('images')) {
            $news = $this->newsRepository->saveFiles($news->id, $request);
        }

        return redirect(locale_route('blog.index', $news->id))->with('success', __('admin.create_successfully'));
        // return redirect(locale_route('news.show', $news->id))->with('success', __('admin.create_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param string $locale
     * @param Product $product
     *
     * @return Application|Factory|View
     */
    public function show(string $locale, News $news)
    {
        return view('admin.pages.news.show', [
            'news' => $news,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $locale
     * @param Category $category
     *
     * @return Application|Factory|View
     */
    public function edit(string $locale, News $news, $code)
    {
        $newss = $news->where('id', $code)->with('files', 'translations')->first();
        $menu = Menu::all();
        $url = locale_route('blog.update', $code, false);
        $method = 'PUT';

        return view('admin.nowa.views.blogs.form1', [
            'news' => $newss,
            'newss' => $newss,
            'url' => $url,
            'menus' =>  $menu,
            'method' => $method,
        ]);
    }

    public function update(NewsRequest $request, string $locale, News $news, $code)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        $saveData['visible'] = isset($saveData['visible']) && (bool)$saveData['visible'];
        $this->newsRepository->update($code, $saveData);
         
        // dd($request->file('icon_header'));
        if ($request->hasFile('icon_header')) {
            $file = $request->file('icon_header');
            $model = $news::where('id', $code)->first();

            if ($model->file) {
                Storage::delete($model->file->getFileUrlAttribute());
                $model->file->delete();
            }
        }

        if ($request->hasFile('icon_header')) {
            // Get Name Of model
            $reflection = new ReflectionClass(get_class($news));
            $modelName = $reflection->getShortName();
            $file = $request->file('icon_header');
            //dd($modelName);
            $imagename = date('Ymhs') . str_replace(' ', '', $file->getClientOriginalName());
            $destination = base_path() . '/storage/app/public/' . $modelName . '/' . $code . '/icon/';
            $request->file('icon_header')->move($destination, $imagename);
            // $model = $news->where('id', $code)->first();
            $news->file()->create([
                'title' => $imagename,
                'path' => '/storage/app/public/' . $modelName . '/' . $code,
                'format' => $file->getClientOriginalExtension(),
                'type' => File::FILE_DEFAULT,
                'header_icon' => true,
                'fileable_id' => $code,
                'icon' => 'icon_header',
            ]);
            // dd($img);
        }
        // save icons
        // dd($request->file());
        // if (null != $request->file('icon_header')) {
        //     // Get Name Of model
        //     $file = $request->file('icon_header');
        //     $modelName = "Blog";
        //     $img = \Image::make($file);
        //     $imagename = date('Ymhs') . str_replace(' ', '', $file->getClientOriginalName());
        //     $destination = base_path() . '/storage/app/public/' . $modelName . '/' . $code;
        //     if (!file_exists($destination)) {
        //         mkdir($destination, 666, true);
        //     }
        //     $img->save("{$destination}/{$imagename}", 20);
        //     $news->files()->create([
        //         'title' => $imagename,
        //         'path' => 'storage/' . $modelName . '/' . $code,
        //         'format' => $file->getClientOriginalExtension(),
        //         'type' => File::FILE_DEFAULT,
        //         // 'icon' => $imagename,
        //         'fileable_id' => $code,
        //         'header_icon' => $imagename,
        //     ]);
        // }
        // if (null != $request->file('icon_image1')) {
        //     $file = $request->file('icon_image1');
        //     $modelName = "Blog";
        //     $img = \Image::make($file);
        //     $imagename = date('Ymhs') . str_replace(' ', '', $file->getClientOriginalName());
        //     $destination = base_path() . '/storage/app/public/' . $modelName . '/' . $code;
        //     if (!file_exists($destination)) {
        //         mkdir($destination, 666, true);
        //     }
        //     $img->save("{$destination}/{$imagename}", 20);
        //     $news->files()->create([
        //         'title' => $imagename,
        //         'path' => 'storage/' . $modelName . '/' . $code,
        //         'format' => $file->getClientOriginalExtension(),
        //         'type' => File::FILE_DEFAULT,
        //         'icon' => 'icon_image1',
        //         'fileable_id' => $code,
        //     ]);
        // }
        // if (null != $request->file('icon_image2')) {
        //     // Get Name Of model
        //     $file = $request->file('icon_image2');
        //     $modelName = "Blog";
        //     $img = \Image::make($file);
        //     $imagename = date('Ymhs') . str_replace(' ', '', $file->getClientOriginalName());
        //     $destination = base_path() . '/storage/app/public/' . $modelName . '/' . $code;
        //     if (!file_exists($destination)) {
        //         mkdir($destination, 666, true);
        //     }
        //     $img->save("{$destination}/{$imagename}", 20);
        //     $news->files()->create([
        //         'title' => $imagename,
        //         'path' => 'storage/' . $modelName . '/' . $code,
        //         'format' => $file->getClientOriginalExtension(),
        //         'type' => File::FILE_DEFAULT,
        //         'icon' => 'icon_image2',
        //         'fileable_id' => $code,
        //         // 'header_icon' => $imagename,
        //     ]);
        // }
        // if (null != $request->file('icon_image3')) {
        //     // Get Name Of model
        //     $file = $request->file('icon_image3');
        //     $modelName = "Blog";
        //     $img = \Image::make($file);
        //     $imagename = date('Ymhs') . str_replace(' ', '', $file->getClientOriginalName());
        //     $destination = base_path() . '/storage/app/public/' . $modelName . '/' . $code;
        //     if (!file_exists($destination)) {
        //         mkdir($destination, 666, true);
        //     }
        //     $img->save("{$destination}/{$imagename}", 20);
        //     $news->files()->create([
        //         'title' => $imagename,
        //         'path' => 'storage/' . $modelName . '/' . $code,
        //         'format' => $file->getClientOriginalExtension(),
        //         'type' => File::FILE_DEFAULT,
        //         'icon' => 'icon_image3',
        //         'fileable_id' => $code,
        //         // 'header_icon' => $imagename,
        //     ]);
        // }

        $this->newsRepository->saveFiles($code, $request);
        return redirect(locale_route('blog.index', $code))->with('success', __('admin.update_successfully'));
    }


    public function destroy(string $locale, News $news, $code)
    {
        if (!$this->newsRepository->delete($code)) {
            return redirect(locale_route('blog.show', $code))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('blog.index'))->with('success', __('admin.delete_message'));
    }
}
