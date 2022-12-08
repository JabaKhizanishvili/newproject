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
use App\Models\News;
use App\Models\Product;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\Eloquent\NewsRepository;
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

class NewsController extends Controller
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
        return view('admin.nowa.views.news.index', [
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

        $url = locale_route('news.store', [], false);
        $method = 'POST';

        return view('admin.nowa.views.news.form', [
            'news' => $news,
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
        // dd($request->post());
        $saveData = Arr::except($request->except('_token'), []);
        // dd($saveData);
        // $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        // dd($saveData);

        $news = $this->newsRepository->create($saveData);


        // Save Files
        if ($request->hasFile('images')) {
            $news = $this->newsRepository->saveFiles($news->id, $request);
        }

        return redirect(locale_route('news.index', $news->id))->with('success', __('admin.create_successfully'));
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
    public function edit(string $locale, News $news)
    {
        $url = locale_route('news.update', $news->id, false);
        $method = 'PUT';

        return view('admin.nowa.views.news.form', [
            'news' => $news,
            'url' => $url,
            'method' => $method,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductRequest $request
     * @param string $locale
     * @param Product $product
     * @return Application|RedirectResponse|Redirector
     * @throws ReflectionException
     */
    public function update(NewsRequest $request, string $locale, News $news)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];

        $this->newsRepository->update($news->id, $saveData);

        $this->newsRepository->saveFiles($news->id, $request);


        return redirect(locale_route('news.index', $news->id))->with('success', __('admin.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $locale
     * @param Product $product
     * @return Application|RedirectResponse|Redirector
     */
    public function destroy(string $locale, News $news)
    {
        if (!$this->newsRepository->delete($news->id)) {
            return redirect(locale_route('news.show', $news->id))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('news.index'))->with('success', __('admin.delete_message'));
    }
}
