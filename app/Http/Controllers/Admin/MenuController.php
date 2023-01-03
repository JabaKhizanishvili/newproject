<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Menu as News;
use App\Models\Product;
use App\Models\Layout;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\Eloquent\MenuRepository as NewsRepository;
use App\Repositories\NewsRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use ReflectionException;

class MenuController extends Controller
{

    private $newsRepository;

    public function __construct(
        NewsRepository $newsRepository
    ) {
        $this->newsRepository = $newsRepository;
    }


    public function index(NewsRequest $request)
    {
        return view('admin.nowa.views.menu.index', [
            'news' => $this->newsRepository->getData($request, ['translations']),
            'data' => $this->newsRepository->getData($request),
        ]);
    }


    public function create()
    {
        $layout = Layout::all();
        $news = $this->newsRepository->model;
        $url = locale_route('menu.store', [], false);
        $method = 'POST';
        $menus = News::with('translations')->get()->all();

        return view('admin.nowa.views.menu.form', [
            'news' => $news,
            'layout' => $layout,
            'newss' => null,
            'menus' => $menus,
            'url' => $url,
            'method' => $method,
        ]);
    }

    public function store(NewsRequest $request)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        $saveData['show'] = isset($saveData['show']) && (bool)$saveData['show'];
        $news = $this->newsRepository->create($saveData);

        // Save Files
        if ($request->hasFile('images')) {
            $news = $this->newsRepository->saveFiles($news->id, $request);
        }

        return redirect(locale_route('menu.index', $news->id))->with('success', __('admin.create_successfully'));
    }


    public function show(string $locale, News $news)
    {
        return view('admin.pages.news.show', [
            'news' => $news,
        ]);
    }


    public function edit(string $locale, News $news, $code)
    {
        $newss = $news->where('id', $code)->with('files', 'translations')->first();
        $layout = Layout::all();
        // dd($newss, 'esaa');
        $menus = News::with('translations')->where([
            // ["parent_id", "!=", null],
            ["id", "!=", $newss->id],
        ])->get()->all();

        // dd(News::with("translations")->get()->all(), 'esaa');
        $url = locale_route('menu.update', $code, false);
        $method = 'PUT';

        return view('admin.nowa.views.menu.form', [
            "layout" => $layout,
            'news' => $news->where('id', $code)->with('files', 'translations')->first(),
            'newss' => $newss,
            'url' => $url,
            'method' => $method,
            'menus' => $menus,
        ]);
    }

    public function update(NewsRequest $request, string $locale, News $news, $code)
    {
        $layout = Layout::all();
        $saveData = Arr::except($request->except('_token'), []);
        $arr = [
            $request->slider,
            $request->partners,
            $request->reports,
            $request->subscribers,
            $request->blog,
            $request->slider1,
        ];
        $array = [
            'slider', 'partners', 'reports', 'subscribers', 'blog', 'slider1'
        ];
        foreach ($arr as $key => $value) {
            if ($value == null) {
                $saveData += array($array[$key] => $arr[$key]);
            };
        }

        $layout_id = $saveData['layout'];
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        $saveData['show'] = isset($saveData['show']) && (bool)$saveData['show'];
        $saveData['showinfooter'] = isset($saveData['showinfooter']) && (bool)$saveData['showinfooter'];
        $update = $this->newsRepository->update($code, $saveData);
        $this->newsRepository->saveFiles($code, $request);
        return redirect(locale_route('menu.index', $code))->with('success', __('admin.update_successfully'));
    }

    public function destroy(string $locale, News $news, $code)
    {
        if (!$this->newsRepository->delete($code)) {
            return redirect(locale_route('menu.show', $code))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('menu.index'))->with('success', __('admin.delete_message'));
    }
}
