<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Slider;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Http\Request;


class ServiceController extends Controller
{
    public function index()
    {


        $page = Page::where('key', 'classification')->firstOrFail();

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

        return Inertia::render('Services/Classification', ["sliders" => $sliders->get(), "page" => $page, "seo" => [
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


    // stators page render
    public function stators()
    {
        $page = Page::where('key', 'stators')->firstOrFail();
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

        return Inertia::render('Services/Stators/1', ["sliders" => $sliders->get(), "page" => $page, "seo" => [
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

    public function statorsId(Request $request)
    {
        // dd($request->id);
        $page = Page::where('key', 'certification')->firstOrFail();
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

        return Inertia::render('Services/Stators/' . $request->id, ["sliders" => $sliders->get(), "page" => $page, "seo" => [
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

    //   certification page render
    public function Certification()
    {
        $page = Page::where('key', 'certification')->firstOrFail();
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

        return Inertia::render('Services/Certification', ["sliders" => $sliders->get(), "page" => $page, "seo" => [
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
    public function CompRecognition()
    {
        $page = Page::where('key', 'comprecognition')->firstOrFail();
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

        return Inertia::render('Services/CompRecognition', ["sliders" => $sliders->get(), "page" => $page, "seo" => [
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
}
