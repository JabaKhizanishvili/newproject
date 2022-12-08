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
use App\Http\Requests\Admin\StaffRequest;
use App\Models\Category;
use App\Models\Customer;
use App\Models\File;
use App\Models\Product;
use App\Models\Skill;
use App\Models\Staff;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Config;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Eloquent\StaffRepository;
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
use App\Models\Size;
use App\Models\Iron;
// use Clockwork\Request\Request;
use Illuminate\Http\Request;

class IronController1 extends Controller
{

    private $staffRepository;

    public function __construct(
        StaffRepository $staffRepository
    ) {
        $this->staffRepository = $staffRepository;
    }

    public function index(ProductRequest $request)
    {
        //saxelebis gamo aketebs savaraudod modelshi size aris dagav
        return view('admin.nowa.views.irons.index', [
            "zoma" => Iron::with('translations')->get(),
            "sizes" => Size::paginate(5),
            "locale" => Config::get('app.locale'),
            "search" => []
        ]);
    }

    public function create(Request $request)
    {
        // dd($request->post());
        Size::create([
            "iron_id" => $request->product,
            "size" => $request->size,
        ]);
        return redirect()->back();
    }

    public function destroy(Request $request, $locale, $slug)
    {
        // dd($slug);
        Size::where('id', $slug)->delete();
        return redirect()->back();
    }

    public function update(Request $request)
    {
        // dd($request->post());
        Size::where("iron_id", $request->id)->update(["size" => $request->size]);
        return redirect()->back();
    }

    public function search(Request $request)
    {
        $searchSize = Size::where('size', 'LIKE', "%$request->size%")->paginate(5);
        $category = Iron::with("translations")->whereTranslationLike('name', '%' . $request->search . '%')->get();

        // $searchSize = Size::where('size', $request->size)->get();


        function checkSize($search)
        {
            global $request;
            if (count($search) > 0) {
                if ($request->size == null) {
                    return Size::paginate(5);
                } else {
                    return $search;
                }
                return $search;
            } else {
                return [];
            }
        }



        // dd(checkSize($searchSize));
        if ($request->search) {
            if (count($category) > 0) {
                if ($request->search == null) {
                    return view('admin.nowa.views.irons.index', [
                        "zoma" => Iron::with('translations')->get(),
                        "sizes" => Size::paginate(5),
                        "locale" => Config::get('app.locale'),
                        'search' => [],
                    ]);
                };
                $searched = Size::where('iron_id', $category[0]->id)->paginate(5);
                return view('admin.nowa.views.irons.index', [
                    "zoma" => Iron::with('translations')->get(),
                    "sizes" => Size::paginate(5),
                    "locale" => Config::get('app.locale'),
                    'search' => $searched,
                ]);
            }
        } elseif ($request->size) {
            if (count($searchSize) > 0) {
                $searched = Size::where('iron_id', $searchSize[0]->id)->paginate(5);
                return view('admin.nowa.views.irons.index', [
                    "zoma" => Iron::with('translations')->get(),
                    "sizes" => Size::paginate(5),
                    "locale" => Config::get('app.locale'),
                    'search' => checkSize($searchSize),
                ]);
            }
        }

        // if (count($category) > 0) {
        //     if ($request->search == null) {
        //         return view('admin.nowa.views.irons.index', [
        //             "zoma" => Iron::with('translations')->get(),
        //             "sizes" => Size::paginate(5),
        //             "locale" => Config::get('app.locale'),
        //             'search' => [],
        //         ]);
        //     };
        //     $searched = Size::where('iron_id', $category[0]->id)->paginate(5);
        //     return view('admin.nowa.views.irons.index', [
        //         "zoma" => Iron::with('translations')->get(),
        //         "sizes" => Size::paginate(5),
        //         "locale" => Config::get('app.locale'),
        //         'search' => $searched,
        //     ]);
        // } else {
        //     return view('admin.nowa.views.irons.index', [
        //         "zoma" => Iron::with('translations')->get(),
        //         "sizes" => Size::paginate(5),
        //         "locale" => Config::get('app.locale'),
        //         'search' => [],
        //     ]);
        // }

        return view('admin.nowa.views.irons.index', [
            "zoma" => Iron::with('translations')->get(),
            "sizes" => Size::paginate(5),
            "locale" => Config::get('app.locale'),
            'search' => [],
        ]);
    }
}
