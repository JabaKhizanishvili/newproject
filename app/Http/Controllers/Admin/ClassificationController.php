<?php

/**
 *  app/Http/Controllers/Admin/ProductController.php
 *
 * Date-Time: 30.07.21
 * Time: 10:37
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\SkillRequest;
use App\Models\Category;
use App\Models\Classes;
use App\Models\Customer;
use App\Models\File;
use App\Models\Product;
use App\Models\Skill;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\Eloquent\ClassesRepository;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\SkillRepository;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use ReflectionException;
use Illuminate\Support\Facades\Hash;

class ClassificationController extends Controller
{
    /**
     * @var ProductRepositoryInterface
     */
    private $classesRepository;




    public function __construct(
        ClassesRepository $classesRepository
    ) {
        $this->classesRepository = $classesRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(ProductRequest $request)
    {
        /*return view('admin.pages.product.index', [
            'products' => $this->productRepository->getData($request, ['translations', 'categories'])
        ]);*/

        return view('admin.nowa.views.classification.index', [
            'data' => $this->classesRepository->getData($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $classes = $this->classesRepository->model;





        $url = locale_route('classification.store', [], false);
        $method = 'POST';

        /*return view('admin.pages.product.form', [
            'product' => $product,
            'url' => $url,
            'method' => $method,
            'categories' => $this->categories
        ]);*/

        return view('admin.nowa.views.classification.form', [
            'classes' => $classes,
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
    public function store(SkillRequest $request)
    {

        //dd($request->all());
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];



        //dd($saveData);
        $customer = $this->classesRepository->create($saveData);


        // Save Files
        if ($request->hasFile('images')) {
            $customer = $this->classesRepository->saveFiles($customer->id, $request);
        }

        /*if ($request->hasFile('files')) {
            $customer = $this->classesRepository->saveFilesDocs($customer->id, $request);
        }*/

        return redirect(locale_route('classification.index', $customer->id))->with('success', __('admin.create_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param string $locale
     * @param Product $product
     *
     * @return Application|Factory|View
     */
    public function show(string $locale, Customer $product)
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
    public function edit(string $locale, Classes $classes)
    {
        $url = locale_route('classification.update', $classes->id, false);
        $method = 'PUT';

        /*return view('admin.pages.product.form', [
            'product' => $product,
            'url' => $url,
            'method' => $method,
            'categories' => $this->categories
        ]);*/

        return view('admin.nowa.views.classification.form', [
            'skill' => $classes,
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
    public function update(SkillRequest $request, string $locale, Classes $classes)
    {
        //dd($request->all());
        $saveData = Arr::except($request->except('_token'), []);
        //$saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];

        $saveData['password'] = Hash::make($request->password);
        //dd($saveData);

        $this->classesRepository->update($classes->id, $saveData);



        $this->classesRepository->saveFiles($classes->id, $request);

        if ($request->hasFile('files')) {
            $customer = $this->classesRepository->saveFilesDocs($classes->id, $request);
        }




        return redirect(locale_route('classification.index', $classes->id))->with('success', __('admin.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $locale
     * @param Product $product
     * @return Application|RedirectResponse|Redirector
     */
    public function destroy(string $locale, Classes $classes)
    {
        if (!$this->classesRepository->delete($classes->id)) {
            return redirect(locale_route('classification.index', $classes->id))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('classification.index'))->with('success', __('admin.delete_message'));
    }

    public function docDelete($locale, $id)
    {
        $file = File::query()->where('id', $id)->firstOrFail();
        $id = $file->fileable_id;
        //dd($file);
        if (Storage::exists('public/Customer/' . $file->fileable_id . '/files/' . $file->title)) {
            Storage::delete('public/Customer/' . $file->fileable_id . '/files/' . $file->title);
        }

        $file->delete();
        return redirect(locale_route('classification.edit', $id))->with('success', __('admin.delete_message'));
    }
}
