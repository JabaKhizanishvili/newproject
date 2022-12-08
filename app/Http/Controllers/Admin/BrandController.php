<?php

/**
 *  app/Http/Controllers/Admin/CategoryController.php
 *
 * Date-Time: 30.07.21
 * Time: 09:18
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brands;
use App\Models\File;
use App\Models\Language;
use App\Models\Translations\CategoryTranslation;
use App\Repositories\BrandRepositoryInterface;
use App\Repositories\Eloquent\BrandRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Locale;

class BrandController extends Controller
{
    private $brandRepository;

    public function __construct(
        BrandRepository $brandRepository
    ) {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(BrandRequest $request)
    {
        /*return view('admin.pages.product.index', [
            'products' => $this->productRepository->getData($request, ['translations', 'categories'])
        ]);*/

        return view('admin.nowa.views.brands.index', [
            'data' => $this->brandRepository->getData($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $brand = $this->brandRepository->model;
        $url = locale_route('brand.store', [], false);
        $method = 'POST';

        return view('admin.nowa.views.brands.form', [
            'url' => $url,
            'method' => $method,
            'brand' => $brand,
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
    public function store(BrandRequest $request)
    {

        //dd($request->all());
        $saveData = Arr::except($request->except('_token'), []);
        //$saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];

        $customer = $this->brandRepository->create($saveData);


        //dd($saveData);


        // Save Files
        if ($request->hasFile('images')) {
            $customer = $this->brandRepository->saveFiles($customer->id, $request);
        }

        return redirect(locale_route('brand.index', $customer->id))->with('success', __('admin.create_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param string $locale
     * @param Product $product
     *
     * @return Application|Factory|View
     */
    public function show(string $locale, Brands $product)
    {
        return view('admin.pages.product.show', [
            'product' => $product,
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
    public function edit(string $locale, Brands $brand)
    {
        $url = locale_route('brand.update', $brand->id, false);
        $method = 'PUT';

        return view('admin.nowa.views.brands.form', [
            'brand' => $brand,
            'url' => $url,
            'method' => $method,
        ]);
    }


    public function update(BrandRequest $request, string $locale, Brands $brand)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];

        if ($this->brandRepository->update($brand->id, $saveData)) {
        }

        $this->brandRepository->saveFiles($brand->id, $request);


        return redirect(locale_route('brand.index', $brand->id))->with('success', __('admin.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $locale
     * @param Product $product
     * @return Application|RedirectResponse|Redirector
     */
    public function destroy(string $locale, Brands $brand)
    {
        if (!$this->brandRepository->delete($brand->id)) {
            return redirect(locale_route('brand.index', $brand->id))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('brand.index'))->with('success', __('admin.delete_message'));
    }

    // public function docDelete($locale, $id)
    // {
    //     $file = File::query()->where('id', $id)->firstOrFail();
    //     $id = $file->fileable_id;
    //     //dd($file);
    //     if (Storage::exists('public/Customer/' . $file->fileable_id . '/files/' . $file->title)) {
    //         Storage::delete('public/Customer/' . $file->fileable_id . '/files/' . $file->title);
    //     }

    //     $file->delete();
    //     return redirect(locale_route('customer.edit', $id))->with('success', __('admin.delete_message'));
    // }
}
