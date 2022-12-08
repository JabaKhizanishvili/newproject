<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Slider;
use App\Models\Vacancy;
use App\Repositories\Eloquent\ResumeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use App\Repositories\Eloquent\ProductRepository;
use App\Models\Product;
use App\Models\Category;



class TilesController extends Controller
{
    public function index()
    {


        $page = Page::where('key', 'home')->firstOrFail();

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

        return Inertia::render(
            'Tiles',
            [
                // "product" => Product::with('latestImage')->where('category_id', ('7'))->paginate(10),
                // "product" => Product::with(['latestImage', 'translations'])->where("category_id", 1)->paginate(10),
                "product" => Product::select('products.*', 'categories.slug')
                    ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->where('categories.slug', '=', 'filebi')
                    ->with(['latestImage', 'translations'])->paginate(10),
                "sliders" => $sliders->get(),
                "page" => $page,
                "seo" => [
                    "title" => $page->meta_title,
                    "description" => $page->meta_description,
                    "keywords" => $page->meta_keyword,
                    "og_title" => $page->meta_og_title,
                    "og_description" => $page->meta_og_description,
                    //            "image" => "imgg",
                    //            "locale" => App::getLocale()
                ],
            ]
        )->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }


    public function tiles2()
    {


        $page = Page::where('key', 'home')->firstOrFail();

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

        return Inertia::render(
            'Tiles2',
            [
                "product" => Product::select('products.*', 'categories.slug')
                    ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->where('categories.slug', '=', 'kafeli')
                    ->with(['latestImage', 'translations'])->paginate(10),
                "product1" => Product::select('products.*', 'categories.slug')
                    ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->where('categories.slug', '=', 'metlaxi')
                    ->with(['latestImage', 'translations'])->paginate(10),
                "sliders" => $sliders->get(),
                "page" => $page,
                "seo" => [
                    "title" => $page->meta_title,
                    "description" => $page->meta_description,
                    "keywords" => $page->meta_keyword,
                    "og_title" => $page->meta_og_title,
                    "og_description" => $page->meta_og_description,
                    //            "image" => "imgg",
                    //            "locale" => App::getLocale()
                ],
            ]
        )->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }
    public function tiles3()
    {


        $page = Page::where('key', 'home')->firstOrFail();

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

        return Inertia::render(
            'Laminate',
            [
                "product" => Product::select('products.*', 'categories.slug')
                    ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->where('categories.slug', '=', 'laminati')
                    ->with(['latestImage', 'translations'])->paginate(10),
                // "product1" => Product::with(['latestImage', 'translations'])->where("category_id", 5)->paginate(10),
                "sliders" => $sliders->get(),
                "page" => $page,
                "seo" => [
                    "title" => $page->meta_title,
                    "description" => $page->meta_description,
                    "keywords" => $page->meta_keyword,
                    "og_title" => $page->meta_og_title,
                    "og_description" => $page->meta_og_description,
                    //            "image" => "imgg",
                    //            "locale" => App::getLocale()
                ],
            ]
        )->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function doors()
    {
        $page = Page::where('key', 'home')->firstOrFail();

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

        return Inertia::render(
            'Doors',
            [
                // "staff" => Staff::all(),
                "product" => Product::select('products.*', 'categories.slug')
                    ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->where('categories.slug', '=', 'ironDoors')
                    ->with(['latestImage', 'translations'])->paginate(10),
                "product1" => Product::select('products.*', 'categories.slug')
                    ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->where('categories.slug', '=', 'woodDoors')
                    ->with(['latestImage', 'translations'])->paginate(10),
                "sliders" => $sliders->get(),
                "page" => $page,
                "seo" => [
                    "title" => $page->meta_title,
                    "description" => $page->meta_description,
                    "keywords" => $page->meta_keyword,
                    "og_title" => $page->meta_og_title,
                    "og_description" => $page->meta_og_description,
                    //            "image" => "imgg",
                    //            "locale" => App::getLocale()
                ],
            ]
        )->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function bathroom()
    {


        $page = Page::where('key', 'home')->firstOrFail();

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

        return Inertia::render(
            'Bathroom',
            [
                // "staff" => Staff::all(),
                "product" => Product::select('products.*', 'categories.slug')
                    ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
                    ->where('categories.slug', '=', 'bathroom')
                    ->with(['latestImage', 'translations'])->paginate(10),
                "sliders" => $sliders->get(),
                "page" => $page,
                "seo" => [
                    "title" => $page->meta_title,
                    "description" => $page->meta_description,
                    "keywords" => $page->meta_keyword,
                    "og_title" => $page->meta_og_title,
                    "og_description" => $page->meta_og_description,
                    //            "image" => "imgg",
                    //            "locale" => App::getLocale()
                ],
            ]
        )->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function singleproduct()
    {


        $page = Page::where('key', 'home')->firstOrFail();

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

        return Inertia::render(
            'SingleProduct',
            [
                // "staff" => Staff::all(),
                "sliders" => $sliders->get(),
                "page" => $page,
                "seo" => [
                    "title" => $page->meta_title,
                    "description" => $page->meta_description,
                    "keywords" => $page->meta_keyword,
                    "og_title" => $page->meta_og_title,
                    "og_description" => $page->meta_og_description,
                    //            "image" => "imgg",
                    //            "locale" => App::getLocale()
                ],
            ]
        )->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }


    public function show(string $locale, $slug)
    {
        $page = Page::where('key', 'home')->firstOrFail();
        $product = Product::with('latestImage')->find($slug);
        // dd($product->category_id);
        return Inertia::render('SingleProduct', [
            "product" => Product::with('latestImage')->find($slug),
            // "sameproduct" => Product::with('latestImage')->find($slug),
            "sameproduct" => Product::where([
                ['id', '!=', $slug],
                ['category_id', '=', $product->category_id],
            ])->with('latestImage')->latest()->limit(6)->get(),
            "seo" => [
                "title" => $page->meta_title ?? $page->meta_title,
                "description" => $page->meta_description ?? $page->meta_description,
                "keywords" => $page->meta_keyword ?? $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,
            ]
        ])->withViewData([
            'meta_title' => $page->meta_title ?? $page->meta_title,
            'meta_description' => $page->meta_description ?? $page->meta_description,
            'meta_keyword' => $page->meta_keyword ?? $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function search(Request $request)
    {
        $searchedProducts = Product::with(['latestImage', 'translation'])->whereTranslationLike('title', '%' . $request->get('query') . '%')
            ->orWhereTranslationLike('description', '%' . $request->get('query') . '%')->get();
        // dd($searchedProducts);


        $page = Page::where('key', 'home')->firstOrFail();

        $images = [];
        foreach ($page->sections as $sections) {
            if ($sections->file) {
                $images[] = asset($sections->file->getFileUrlAttribute());
            } else {
                $images[] = null;
            }
        }

        $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);

        return Inertia::render(
            'Search',
            [
                "search" => $searchedProducts,
                // "staff" => Staff::all(),
                "product" => Product::with(['latestImage', 'translations'])->where("category_id", 7)->paginate(10),
                "sliders" => $sliders->get(),
                "page" => $page,
                "seo" => [
                    "title" => $page->meta_title,
                    "description" => $page->meta_description,
                    "keywords" => $page->meta_keyword,
                    "og_title" => $page->meta_og_title,
                    "og_description" => $page->meta_og_description,
                    //            "image" => "imgg",
                    //            "locale" => App::getLocale()
                ],
            ]
        )->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }
}
