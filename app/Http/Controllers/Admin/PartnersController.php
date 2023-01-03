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
use App\Models\Partner as News;
use App\Models\Product;
use App\Repositories\CategoryRepositoryInterface;
// use App\Repositories\Eloquent\MenuRepository as NewsRepository;
use App\Repositories\Eloquent\PartnerRepository as NewsRepository;
use App\Repositories\NewsRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use ReflectionException;

class PartnersController extends Controller
{

    private $newsRepository;

    public function __construct(
        NewsRepository $newsRepository
    ) {
        $this->newsRepository = $newsRepository;
    }

    public function index(NewsRequest $request)
    {
        return view('admin.nowa.views.partners.index', [
            'news' => $this->newsRepository->getData($request, ['translations']),
            'data' => $this->newsRepository->getData($request),
        ]);
    }

    public function create()
    {
        $news = $this->newsRepository->model;
        $url = locale_route('partner.store', [], false);
        $method = 'POST';

        return view('admin.nowa.views.partners.form', [
            'news' => $news,
            'url' => $url,
            'method' => $method,
        ]);
    }

    public function store(NewsRequest $request)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $news = $this->newsRepository->create($saveData);

        // Save Files
        if ($request->hasFile('images')) {
            $news = $this->newsRepository->saveFiles($news->id, $request);
        }

        return redirect(locale_route('partner.index', $news->id))->with('success', __('admin.create_successfully'));
    }


    public function show(string $locale, News $news)
    {
        return view('admin.pages.news.show', [
            'news' => $news,
        ]);
    }


    public function edit(string $locale, News $news, $code)
    {
        $url = locale_route('partner.update', $code, false);
        $method = 'PUT';

        return view('admin.nowa.views.partners.form', [
            'news' => $news->where('id', $code)->with('files', 'translations')->first(),
            'url' => $url,
            'method' => $method,
        ]);
    }

    public function update(NewsRequest $request, string $locale, News $news, $code)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];

        $update = $this->newsRepository->update($code, $saveData);
        // dd($update);

        $this->newsRepository->saveFiles($code, $request);


        return redirect(locale_route('partner.index', $code))->with('success', __('admin.update_successfully'));
    }

    public function destroy(string $locale, News $news, $code)
    {
        if (!$this->newsRepository->delete($code)) {
            return redirect(locale_route('partner.show', $code))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('partner.index'))->with('success', __('admin.delete_message'));
    }
}
