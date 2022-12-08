<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Category;
use App\Models\Product;
use App\Models\Portfolio;
use App\Models\Staff;
use App\Models\News;
use App\Models\Slider;
use App\Models\Slider2;
use App\Models\UpcomingEvent;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Repositories\Eloquent\ProductRepository;
use Clockwork\Request\Request;
use Dotenv\Util\Regex;
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // $portfolio = Portfolio::where("status", 0)->with(['files', 'translations', 'latestImage'])->paginate(6);

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
        // $query =  Product::select('products.*', 'categories.slug')
        //     ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
        //     ->where('categories.slug', '=', 'filebi')
        //     ->with(['latestImage', 'translations'])->paginate(6);

        return Inertia::render('Home', [
            // "category" => Category::with('translations')->get(),
            "links" => asset('storage/images/slider2logo'),
            "news" => News::with('translations', 'file')->orderBy('created_at', 'desc')->limit(3)->get(),
            "UpcomingEvent" => UpcomingEvent::with(['file', 'translations'])->get(),
            "slider2" => Slider2::with('translations', 'file')->get(),
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
