<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index()
    {
        // $news = News::where("status", 1)->with(['file', 'translations'])->paginate(3);
        //        dd(1);
        $page = Page::where('key', 'home')->firstOrFail();
        $news = News::with(['file', 'translations', 'latestImage'])->paginate(8);


        // return Inertia::render('News', ["seo" => [
        //     "title" => $page->meta_title,
        //     "description" => $page->meta_description,
        //     "keywords" => $page->meta_keyword,
        //     "og_title" => $page->meta_og_title,
        //     "og_description" => $page->meta_og_description,
        //     //            "image" => "imgg",
        //     //            "locale" => App::getLocale()
        // ]])->withViewData([
        //     'meta_title' => $page->meta_title,
        //     'meta_description' => $page->meta_description,
        //     'meta_keyword' => $page->meta_keyword,
        //     "image" => $page->file,
        //     'og_title' => $page->meta_og_title,
        //     'og_description' => $page->meta_og_description
        // ]);



        return Inertia::render(
            'News',
            [
                // "product" => Product::with('latestImage')->where('category_id', ('7'))->paginate(10),
                "news" => $news,
                // "product" => Product::with(['latestImage', 'translations'])->where("category_id", 1)->paginate(10),
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
        $news = News::where("id", $slug)->with(['file', 'translations', 'latestImage'])->firstOrFail();
        // $lastNews = News::where("status", 1)->where('slug', '<>', $slug)->latest()->with(["file", "translations"])->take(3)->get();
        $page = Page::where('key', 'home')->firstOrFail();



        return Inertia::render('SingleNews', ["news" => $news, "seo" => [
            'news' => $news,
            "title" => $news->meta_title ?? $page->meta_title,
            "description" => $news->meta_description ?? $page->meta_description,
            "keywords" => $news->meta_keyword ?? $page->meta_keyword,
            "og_title" => $page->meta_og_title,
            "og_description" => $page->meta_og_description,
            //            "image" => "imgg",
            //            "locale" => App::getLocale()
        ]])->withViewData([
            'meta_title' => $news->meta_title ?? $page->meta_title,
            'meta_description' => $news->meta_description ?? $page->meta_description,
            'meta_keyword' => $news->meta_keyword ?? $page->meta_keyword,
            "image" => $news->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }
}
