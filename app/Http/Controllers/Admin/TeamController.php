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
use App\Http\Requests\Admin\TeamRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\File;
use App\Models\Product;
use App\Models\Skill;
use App\Models\Team;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\TeamRepository;
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

class TeamController extends Controller
{
    /**
     * @var ProductRepositoryInterface
     */
    private $teamRepository;




    public function __construct(
        TeamRepository $teamRepository
    ) {
        $this->teamRepository = $teamRepository;
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

        return view('admin.nowa.views.team.index', [
            'data' => $this->teamRepository->getData($request),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $team = $this->teamRepository->model;
        $url = locale_route('team.store', [], false);
        $method = 'POST';

        /*return view('admin.pages.product.form', [
            'product' => $product,
            'url' => $url,
            'method' => $method,
            'categories' => $this->categories
        ]);*/

        return view('admin.nowa.views.team.form', [
            'url' => $url,
            'method' => $method,
            'team' => $team,
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
    public function store(TeamRequest $request)
    {

        // dd($request->all());
        $saveData = Arr::except($request->except('_token'), []);
        //$saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];

        $customer = $this->teamRepository->create($saveData);


        //dd($saveData);


        // Save Files
        if ($request->hasFile('images')) {
            $customer = $this->teamRepository->saveFiles($customer->id, $request);
        }

        return redirect(locale_route('team.index', $customer->id))->with('success', __('admin.create_successfully'));
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
    public function edit(string $locale, Team $team)
    {
        $url = locale_route('team.update', $team->id, false);
        $method = 'PUT';

        /*return view('admin.pages.product.form', [
            'product' => $product,
            'url' => $url,
            'method' => $method,
            'categories' => $this->categories
        ]);*/

        return view('admin.nowa.views.team.form', [
            'team' => $team,
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
    public function update(TeamRequest $request, string $locale, Team $team)
    {
        //dd($request->all());
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];


        //dd($staff->id);

        if ($this->teamRepository->update($team->id, $saveData)) {
        }

        $this->teamRepository->saveFiles($team->id, $request);


        return redirect(locale_route('team.index', $team->id))->with('success', __('admin.update_successfully'));
    }
    public function destroy(string $locale, Team $staff, $code)
    {
        if (!$this->teamRepository->delete($code)) {
            return redirect(locale_route('staff.index', $code))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('staff.index'))->with('success', __('admin.delete_message'));
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
        return redirect(locale_route('customer.edit', $id))->with('success', __('admin.delete_message'));
    }
}
