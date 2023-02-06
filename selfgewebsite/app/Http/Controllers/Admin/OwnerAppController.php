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
use App\Models\Customer;
use App\Models\File;
use App\Models\Product;
use App\Models\Ownerapplication;
use App\Models\Skill;
use App\Repositories\CategoryRepositoryInterface;
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
use Illuminate\Http\Request;

class OwnerAppController extends Controller
{
    /**
     * @var ProductRepositoryInterface
     */
    private $skillRepository;




    public function __construct(
        SkillRepository $skillRepository
    ) {
        $this->skillRepository = $skillRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(ProductRequest $request, Ownerapplication $owner)
    {
        /*return view('admin.pages.product.index', [
            'products' => $this->productRepository->getData($request, ['translations', 'categories'])
        ]);*/

        return view('admin.nowa.views.apps.ownerapp', [
            // 'data' => $this->skillRepository->getData($request),
            'data'  => $owner->all()
        ]);
    }

    public function details(Request $req, $locale, Ownerapplication $owner, $id = "")
    {
        return view('admin.nowa.views.apps.appdetail', [
            'data'  => $owner->find($id)
        ]);
    }
}
