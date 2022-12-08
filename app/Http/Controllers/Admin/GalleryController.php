<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryRequest;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Product;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\GalleryRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;

class GalleryController extends Controller
{
    /**
     * @var GalleryRepositoryInterface
     */
    private $galleryRepository;

    /**
     * @param GalleryRepositoryInterface $galleryRepository
     */
    public function __construct(
        GalleryRepositoryInterface  $galleryRepository
    )
    {
        $this->galleryRepository = $galleryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(GalleryRequest $request)
    {
        /*return view('admin.pages.gallery.index', [
            'galleries' => $this->galleryRepository->getData($request)
        ]);*/

        return view('admin.nowa.views.gallery.index', [
            'galleries' => $this->galleryRepository->getData($request)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $gallery = $this->galleryRepository->model;

        $url = locale_route('gallery.store', [], false);
        $method = 'POST';

//        dd($gallery);

        /*return view('admin.pages.gallery.form', [
            'gallery' => $gallery,
            'url' => $url,
            'method' => $method,
        ]);*/

        return view('admin.nowa.views.gallery.form', [
            'gallery' => $gallery,
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
    public function store(GalleryRequest $request)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];

        $gallery = $this->galleryRepository->create($saveData);

        // Save Files
        if ($request->hasFile('images')) {
            $product = $this->galleryRepository->saveFiles($gallery->id, $request);
        }

        return redirect(locale_route('gallery.edit', $gallery->id))->with('success', __('admin.create_successfully'));

    }

    /**
     * Display the specified resource.
     *
     * @param string $locale
     * @param Product $product
     *
     * @return Application|Factory|View
     */
    public function show(string $locale, Gallery $gallery)
    {
        return view('admin.pages.gallery.show', [
            'gallery' => $gallery,
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
    public function edit(string $locale, Gallery $gallery)
    {
        $url = locale_route('gallery.update', $gallery->id, false);
        $method = 'PUT';

        /*return view('admin.pages.gallery.form', [
            'gallery' => $gallery,
            'url' => $url,
            'method' => $method,

        ]);*/

        return view('admin.nowa.views.gallery.form', [
            'gallery' => $gallery,
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
    public function update(GalleryRequest $request, string $locale, Gallery $gallery)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];

        $this->galleryRepository->update($gallery->id, $saveData);

        $this->galleryRepository->saveFiles($gallery->id, $request);


        return redirect(locale_route('gallery.index', $gallery->id))->with('success', __('admin.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $locale
     * @param Product $product
     * @return Application|RedirectResponse|Redirector
     */
    public function destroy(string $locale, Gallery $gallery)
    {
        if (!$this->galleryRepository->delete($gallery->id)) {
            return redirect(locale_route('gallery.show', $gallery->id))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('gallery.index'))->with('success', __('admin.delete_message'));
    }
}
