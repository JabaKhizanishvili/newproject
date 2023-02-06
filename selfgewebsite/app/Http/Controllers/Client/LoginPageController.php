<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Staff;
use App\Models\Team;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Support\Facades\Auth;


class LoginPageController extends Controller
{

    public function __construct()
    {
        //$this->middleware('guest:customer')->except('logout');
    }

    public function Login(Request $request)
    {


        $page = Page::where('key', 'login')->firstOrFail();

        $images = [];
        foreach ($page->sections as $sections) {
            if ($sections->file) {
                $images[] = asset($sections->file->getFileUrlAttribute());
            } else {
                $images[] = null;
            }
        }

        $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);
        //        dd($page->file);
        //        dd(App::getLocale());
        $products = app(ProductRepository::class)->getPopularProducts();


        //dd($products);

        return Inertia::render('SignIn', ['success' => $request->session()->get('success'), "sliders" => $sliders->get(), "page" => $page, "seo" => [
            "title" => $page->meta_title,
            "description" => $page->meta_description,
            "keywords" => $page->meta_keyword,
            "og_title" => $page->meta_og_title,
            "og_description" => $page->meta_og_description,

            //            "image" => "imgg",
            //            "locale" => App::getLocale()
        ], 'popular_products' => $products, 'images' => $images])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function Register()
    {
        $page = Page::where('key', 'login')->firstOrFail();

        $images = [];
        foreach ($page->sections as $sections) {
            if ($sections->file) {
                $images[] = asset($sections->file->getFileUrlAttribute());
            } else {
                $images[] = null;
            }
        }

        $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);
        //        dd($page->file);
        //        dd(App::getLocale());
        $products = app(ProductRepository::class)->getPopularProducts();


        //dd($products);

        return Inertia::render('SignUp', ["sliders" => $sliders->get(), "page" => $page, "seo" => [
            "title" => $page->meta_title,
            "description" => $page->meta_description,
            "keywords" => $page->meta_keyword,
            "og_title" => $page->meta_og_title,
            "og_description" => $page->meta_og_description,

            //            "image" => "imgg",
            //            "locale" => App::getLocale()
        ], 'popular_products' => $products, 'images' => $images])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function auth(Request $request)
    {
        // dd($request->post());

        $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // return redirect()->intended('dashboard');
            return redirect(route("client.cabinet"));
        }

        // if (!Auth::guard('customer')->attempt([
        //     'email' => $request->email,
        //     'password' => $request->password,
        // ], $request->remember)) {
        //return back()->with('danger','Email or Password is incorrect!');
        return redirect()->back()->with('danger', 'wrong login and / or password');
        // }
        $request->session()->regenerate();
        //dd('ok');
        return redirect(locale_route('client.cabinet'));
    }
    public function aboutus(Request $request)
    {

        $page = Page::where('key', 'aboutus')->firstOrFail();

        $images = [];
        foreach ($page->sections as $sections) {
            if ($sections->file) {
                $images[] = asset($sections->file->getFileUrlAttribute());
            } else {
                $images[] = null;
            }
        }

        $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);
        //        dd($page->file);
        //        dd(App::getLocale());
        // $products = app(ProductRepository::class)->getPopularProducts();


        //dd($products);

        return Inertia::render('About', [
            "page" => $page,
            "category" => Category::with('translations')->get(),
            // "partners" => Staff::with('latestImage')->get(),
            // "team" => Team::with('latestImage')->paginate(9),
            'success' => $request->session()->get('success'),
            // "sliders" => $sliders->get(), "page" => $page, "seo" => [
            //     "title" => $page->meta_title,
            //     "description" => $page->meta_description,
            //     "keywords" => $page->meta_keyword,
            //     "og_title" => $page->meta_og_title,
            //     "og_description" => $page->meta_og_description,

            //     //            "image" => "imgg",
            //     //            "locale" => App::getLocale()
            // ],
            // 'popular_products' => $products,
            'images' => $images
        ])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }


    public function partners(Request $request)
    {

        $page = Page::where('key', 'partners')->firstOrFail();

        $images = [];
        foreach ($page->sections as $sections) {
            if ($sections->file) {
                $images[] = asset($sections->file->getFileUrlAttribute());
            } else {
                $images[] = null;
            }
        }

        $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);
        //        dd($page->file);
        //        dd(App::getLocale());
        // $products = app(ProductRepository::class)->getPopularProducts();


        //dd($products);

        return Inertia::render('Partners', [
            'partners' => Staff::with('latestImage')->get(),
            'success' => $request->session()->get('success'),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,

                //            "image" => "imgg",
                //            "locale" => App::getLocale()
            ],
            // 'popular_products' => $products,
            'images' => $images
        ])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }
}
