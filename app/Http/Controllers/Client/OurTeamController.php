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
use App\Models\Staff;



class OurTeamController extends Controller
{
    public function index()
    {


        $page = Page::where('key', 'office')->firstOrFail();

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

        return Inertia::render(
            'OurTeam/Office',
            [
                // "staff" => Staff::all(),
                "staff" => Staff::with('latestImage')->get(),
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
    public function inspectors()
    {


        $page = Page::where('key', 'inspectors')->firstOrFail();

        $images = [];
        foreach ($page->sections as $sections) {
            if ($sections->file) {
                $images[] = asset($sections->file->getFileUrlAttribute());
            } else {
                $images[] = null;
            }
        }

        // $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);
        //        dd($page->file);
        //        dd(App::getLocale());
        $products = app(ProductRepository::class)->getPopularProducts();


        //dd($products);

        return Inertia::render('Inspectors/Inspectors', ["page" => $page, "seo" => [
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
    public function career()
    {


        $page = Page::where('key', 'career')->firstOrFail();

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

        $vacancies = Vacancy::query()->where('status', 1)->with(['translation'])->get();


        //dd($vacancies);

        return Inertia::render('Career/Career', ["sliders" => $sliders->get(), "page" => $page, 'vacancies' => $vacancies, "seo" => [
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

    public function viewVacancy($locale, $slug)
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
        $vacancy = Vacancy::query()->where('slug', $slug)->with(['translation', 'docs', 'skills', 'skills.translation'])->firstOrFail();

        $vacancies = Vacancy::query()->where('status', 1)->where('id', '!=', $vacancy->id)->with(['translation', 'skills', 'skills.translation'])->get();


        //dd($vacancies);

        return Inertia::render('Career/Details/1', [
            "sliders" => $sliders->get(), "page" => $page, 'vacancies' => $vacancies,
            'vacancy' => $vacancy,
            "seo" => [
                "title" => $vacancy->title,
                "description" => $vacancy->description,
                "keywords" => $vacancy->title,
                "og_title" => $vacancy->title,
                "og_description" => $vacancy->description,

                //            "image" => "imgg",
                //            "locale" => App::getLocale()
            ], 'images' => $images
        ])->withViewData([
            'meta_title' => $vacancy->title,
            'meta_description' => $vacancy->description,
            'meta_keyword' => $vacancy->title,
            "image" => $vacancy->file,
            'og_title' => $vacancy->title,
            'og_description' => $vacancy->description,
        ]);
    }

    public function appUpload(Request $request)
    {
        $request->validate([
            'doc' => 'required'
        ]);

        app(ResumeRepository::class)->saveResume($request);
        return redirect()->back();
    }
}
