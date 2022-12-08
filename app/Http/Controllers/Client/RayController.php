<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Category;
use App\Models\Product;
use App\Models\Team;
use App\Models\Portfolio;
use App\Models\Staff;
use App\Models\Content;
use App\Models\Slider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Repositories\Eloquent\ProductRepository;
use Clockwork\Request\Request;
use Dotenv\Util\Regex;
use Illuminate\Support\Facades\Redis;
use Session;

class RayController extends Controller
{
    public function index(Request $request)
    {
        // $portfolio = Portfolio::where("status", 0)->with(['files', 'translations', 'latestImage'])->paginate(6);
        $page = Page::where('key', 'rayacademy')->firstOrFail();

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
        // $query =  Product::select('products.*', 'categories.slug')
        //     ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
        //     ->where('categories.slug', '=', 'filebi')
        //     ->with(['latestImage', 'translations'])->paginate(6);

        return Inertia::render('RayAcademy', [
            "courses" => Team::with("files", "translations")->get(),
            // "category" => Category::with('translations')->get(),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,

                //            "image" => "imgg",
                //            "locale" => App::getLocale()
            ],  'images' => $images
        ])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function show(Request $request, $locale, $course)
    {
        // dd($course);
        $page = Page::where('key', 'rayacademy')->firstOrFail();

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
        // $query =  Product::select('products.*', 'categories.slug')
        //     ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
        //     ->where('categories.slug', '=', 'filebi')
        //     ->with(['latestImage', 'translations'])->paginate(6);
        return Inertia::render('SingleCourse', [
            'success' => Session::get('success'),
            "courses" => Team::with("files", "translations")->where('id', $course)->first(),
            "othercourses" => Team::with("files", "translations")->where("id", "!=", $course)->inRandomOrder()->limit(3)->get(),
            // "category" => Category::with('translations')->get(),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,

                //            "image" => "imgg",
                //            "locale" => App::getLocale()
            ],  'images' => $images
        ])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function rayproductrion()
    {
        $page = Page::where('key', 'rayproduction')->firstOrFail();

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
        // $query =  Product::select('products.*', 'categories.slug')
        //     ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
        //     ->where('categories.slug', '=', 'filebi')
        //     ->with(['latestImage', 'translations'])->paginate(6);

        return Inertia::render('RayProduction', [
            // "category" => Category::with('translations')->get(),
            "project" => Staff::with('translations', 'files')->where("company_id", 1)->get(),
            "content" => Content::with('translation', 'files')->where("company_id", 1)->get(),
            "videogallery" => Category::with('translations', 'files')->where("company_id", 1)->get(),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,

                //            "image" => "imgg",
                //            "locale" => App::getLocale()
            ],  'images' => $images
        ])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function rayanimation()
    {
        $page = Page::where('key', 'rayanimation')->firstOrFail();
        $images = [];
        foreach ($page->sections as $sections) {
            if ($sections->file) {
                $images[] = asset($sections->file->getFileUrlAttribute());
            } else {
                $images[] = null;
            }
        }

        $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);
        return Inertia::render('RayAnimation', [
            // "category" => Category::with('translations')->get(),
            "project" => Staff::with('translations', 'files')->where("company_id", 2)->get(),
            "content" => Content::with('translation', 'files')->where("company_id", 2)->get(),
            "videogallery" => Category::with('translations', 'files')->where('company_id', 2)->get(),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,

                //            "image" => "imgg",
                //            "locale" => App::getLocale()
            ],  'images' => $images
        ])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function raycrypto()
    {
        $page = Page::where('key', 'raycrypto')->firstOrFail();
        $images = [];
        foreach ($page->sections as $sections) {
            if ($sections->file) {
                $images[] = asset($sections->file->getFileUrlAttribute());
            } else {
                $images[] = null;
            }
        }

        $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);
        return Inertia::render('RayCrypto', [
            // "category" => Category::with('translations')->get(),
            "project" => Staff::with('translations', 'files')->where("company_id", 3)->get(),
            "content" => Content::with('translation', 'files')->where("company_id", 3)->get(),
            "videogallery" => Category::with('translations', 'files')->where("company_id", 3)->get(),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,

                //            "image" => "imgg",
                //            "locale" => App::getLocale()
            ],  'images' => $images
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
