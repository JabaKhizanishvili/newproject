<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Iconimage;
use App\Models\Page;
use App\Models\Bookvisit;
use Illuminate\Support\Arr;
use App\Models\Menu;
use App\Models\News;
use App\Models\Portfolio;
use App\Models\Blog;
use App\Models\Partner;
use App\Models\Slider;
use App\Models\Slider2;
use App\Models\UpcomingEvent;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    public function menu(Request $request, $locale, $code = null)
    {
        // phpinfo();
        // die;
        // dd(App::getLocale());
        $menuforDisplay = Menu::with('translation')->get()->all();
        $menu = Menu::with("translations")->get()->all();
        $test = Menu::whereTranslation("name", $code)->with('translations')->first();
        if ($test == null) {
            return abort(403, 'Unauthorized action.');
        }
        // function getSlider2($menu)
        // {
        //     static $data = null;
        //     if ($data == null) {
        //         $data = Slider2::where("parent_id", $menu->id)->with('translations', 'file')->get()->all();
        //     }
        //     return $data;
        // }

        $blogsWidget = Blog::with('translation', 'file', 'latestImage', 'iconimages')->where(
            [
                ['publish_end', '>=', date('Y-m-d')],
                ['publish_start', '<=', date('Y-m-d')],
                ['status', '=', 1],
                ['visible', '=', 1],
                ['category', '=', $test->id],
            ]
        )
            ->whereTranslation('visible', 1)
            ->orderBy('created_at', 'desc')->limit(6)->get();

        function getBlogElements($code)
        {
            $currentLocale = App::getLocale();
            $data = null;
            if ($data == null) {
                $locale = App::getLocale();
                $data = Menu::where([
                    ['layout', 5],
                    ['status', 1],
                ])->with('blog', 'translation', 'blog.translations', 'blog.file')->orderBy('created_at', 'desc')
                    ->whereHas('translations', function ($innerQuery) use ($locale) {
                        $innerQuery->where([
                            ['visible', 1],
                            ['status', 1],
                            ['show', 1],
                            ['locale', $locale],
                            ['layout', 5],
                            ['status', 1],
                        ]);
                    })->inRandomOrder()->limit(2)->get();
            }
            return $data;
        }

        // dd(getBlogElements($test));
        // ერთიდაიგივე სამნაირად

        // $menus = Menu::with('translation')
        //     ->whereTranslation("visible", 1)
        //     ->where([
        //         ['status', '1'],
        //         ['show', '1'],
        //     ])
        //     ->get()->all();

        // $menus = Menu::Join('menutranslations', 'menutranslations.menu_id', '=', 'menus.id')
        //     ->where([
        //         ['menutranslations.visible', 1],
        //         ['menutranslations.locale', $locale],
        //         ['menus.status', 1],
        //         ['menus.show', 1],
        //     ])
        //     ->get()->all();
        // ->toSql();

        $menus = Menu::whereHas('translations', function ($innerQuery) use ($locale) {
            $innerQuery->where([
                ['visible', 1],
                ['status', 1],
                ['show', 1],
                ['locale', $locale],
            ]);
        })->get();

        $footerMenu = Menu::whereHas('translations', function ($innerQuery) use ($locale) {
            $innerQuery->where([
                ['visible', 1],
                ['status', 1],
                ['show', 1],
                ['showinfooter', 1],
                ['locale', $locale],
            ]);
        })->get();

        // dd($footerMenu);
        // $menus1 =  DB::select('select * from `menus` inner join `menutranslations` on `menutranslations`.`menu_id` = `menus`.`id` where (`menutranslations`.`visible` = :visible and `menutranslations`.`locale` = :locale and `menus`.`status` = :status and `menus`.`show` = :show');


        $page = Menu::whereTranslation('name', $code)->firstorFail();
        $partnerslink = Page::where('key', 'home')->firstOrFail();

        if ($code == null) {
            $test = Menu::first();
        }
        function mainSlider($menu)
        {
            static $data = null;
            if (isset($menu->id)) {
                if ($data == null) {
                    $data = Slider::where("parent_id", $menu->id)->with('translations', 'file')->get()->all();
                }
            }

            return $data;
        }
        function sliderLike($menu)
        {
            static $data = null;
            if (isset($menu->id)) {
                if ($data == null) {
                    $data = Portfolio::where("parent_id", $menu->id)->with('translations', 'file')->first();
                }
            }

            return $data;
        }



        function Partners($test)
        {
            static $data = null;
            if ($data == null) {
                $data = Partner::with("translation", 'file')->get()->all();
            };
            return $data;
        }

        $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);
        return Inertia::render('Test', [
            'partnerslink' => isset($partnerslink->partnerslink) ? $partnerslink->partnerslink : null,
            'code' => $code,
            'success' => Session::get('success'),
            'getblogelements' => getBlogElements($test),
            'menuforDisplay' => $menuforDisplay,
            "menus" => $menus,
            "footermeny" => $footerMenu,
            "locale" => $locale,
            "partners" => Partners($test),
            "mainSlider" => mainSlider($test),
            "sliderLike" => sliderLike($test),
            "menu" => $test,
            "blogs" => $blogsWidget ? $blogsWidget : null,
            "links" => asset('storage/images/slider2logo'),
            "news" => News::with('translations', 'file')->orderBy('created_at', 'desc')->limit(3)->get(),
            "UpcomingEvent" => UpcomingEvent::with(['file', 'translations'])->get(),
            // "slider2" => Slider2::where("parent_id", $test->id)->with('translations', 'file')->get()->all(),
            "slider2" => Menu::where([
                ['layout', 5],
                ['status', 1],
            ])->with('blog', 'translation', 'blog.translations', 'blog.file')->orderBy('created_at', 'desc')
                ->whereHas('translations', function ($innerQuery) use ($locale) {
                    $innerQuery->where([
                        ['visible', 1],
                        ['status', 1],
                        ['show', 1],
                        ['locale', $locale],
                        ['layout', 5],
                        ['status', 1],
                    ]);
                })->inRandomOrder()->limit(3)->get(),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,
            ],
        ])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function index(Request $request, $locale, $code = null)
    {

        $menu = Menu::with("translations")->get()->all();
        function getBlogElements($code)
        {
            $currentLocale = App::getLocale();
            $data = null;
            if ($data == null) {
                $locale = App::getLocale();
                $data = Menu::where([
                    ['layout', 5],
                    ['status', 1],
                ])->with('blog', 'translation', 'blog.translations', 'blog.file')->orderBy('created_at', 'desc')
                    ->whereHas('translations', function ($innerQuery) use ($locale) {
                        $innerQuery->where([
                            ['visible', 1],
                            ['status', 1],
                            ['show', 1],
                            ['locale', $locale],
                            ['layout', 5],
                            ['status', 1],
                        ]);
                    })->inRandomOrder()->limit(2)->get();
            }
            return $data;
        }
        // $menus = Menu::with('translation')
        //     ->whereTranslation("visible", 1)
        //     ->whereTranslation("locale", $locale)
        //     ->where([
        //         ['status', 1],
        //         ['show', 1],
        //     ])
        //     ->get()->all();

        $menus = Menu::whereHas('translations', function ($innerQuery) use ($locale) {
            $innerQuery->where([
                ['visible', 1],
                ['locale', $locale],
            ]);
        })->get();

        $footerMenu = Menu::whereHas('translations', function ($innerQuery) use ($locale) {
            $innerQuery->where([
                ['visible', 1],
                ['status', 1],
                ['show', 1],
                ['showinfooter', 1],
                ['locale', $locale],
            ]);
        })->get();

        $test = Menu::whereHas('translations', function ($innerQuery) use ($locale) {
            $innerQuery->where([
                ['visible', 1],
                ['locale', $locale],
            ]);
        })->first();

        // $page = Page::where('key', 'home')->firstOrFail();
        $page = Menu::whereTranslation('name', $test->name)->firstOrFail();
        $images = [];

        if ($code == null) {
            $test = Menu::first();
            $code = $test->name;
        }

        $sliders = Slider::query()->where("status", 1)->with(['file', 'translations']);

        function Partners($test)
        {
            static $data = null;
            if ($test->partners != null) {
                $data = Partner::with("translation", 'file')->get()->all();
            };
            return $data;
        }

        // function mainSlider($menu)
        // {
        //     static $data = null;
        //     if ($menu->slider != null) {
        //         $data = Slider::with("translations", 'file')->get()->all();
        //     }
        //     return $data;
        // }

        function mainSlider($menu)
        {
            static $data = null;
            if (isset($menu->id)) {
                if ($data == null) {
                    $data = Slider::where("parent_id", $menu->id)->with('translations', 'file')->get()->all();
                }
            }

            return $data;
        }

        function sliderLike($menu)
        {
            static $data = null;
            if (isset($menu->id)) {
                if ($data == null) {
                    $data = Portfolio::where("parent_id", $menu->id)->with('translations', 'file')->first();
                }
            }

            return $data;
        }
        $partnerslink = Page::where('key', 'home')->firstOrFail();
        return Inertia::render('Test', [
            'partnerslink' => isset($partnerslink->partnerslink) ? $partnerslink->partnerslink : null,
            'code' => $code,
            'success' => Session::get('success'),
            "blogs" => Blog::with('translation', 'file', 'latestImage', 'iconimages')->where(
                [
                    ['publish_end', '>=', date('Y-m-d')],
                    ['publish_start', '<=', date('Y-m-d')],
                    ['status', '=', 1],
                    ['visible', '=', 1],
                    ['category', '=', $test->id],
                ]
            )
                ->whereTranslation('visible', 1)
                ->orderBy('created_at', 'desc')->limit(6)->get(),
            "menus" => $menus,
            "mainSlider" => mainSlider($test),
            "sliderLike" => sliderLike($test),
            "footermeny" => $footerMenu,
            'getblogelements' => getBlogElements($test),
            "partners" => Partners($test),
            "menu" => $test,
            "links" => asset('storage/images/slider2logo'),
            "news" => News::with('translations', 'file')->orderBy('created_at', 'desc')->limit(3)->get(),
            "UpcomingEvent" => UpcomingEvent::with(['file', 'translations'])->get(),
            // "slider2" => Slider2::with('translations', 'file')->get(),
            "slider2" => getBlogElements($test),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,

            ],
        ])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }


    public function bookvisit(Request $request)
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

        return Inertia::render('MakeVisit', [
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

    public function bookvisitform(Request $request)
    {
        $request->validate([
            "name" => "required",
            "company" => "required",
            "position" => "required",
            "phone" => "required",
            "mail" => "required",
            "quantity" => "required",
            "contact" => "required",
        ]);
        $saveData = Arr::except($request->except('_token'), []);
        Bookvisit::create($saveData);
        return redirect()->back()->with('success', 'warmatebit');
    }

    public function vaccancy(Request $request)
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

        return Inertia::render('Vaccancy', [
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


    public function SingleVaccancy(Request $request, $locale, $code)
    {
        // $menus = Menu::with('translations')->get()->all();
        $menu = Menu::with('translation')
            ->whereTranslation("visible", 1)
            ->where([
                ['status', 1],
                ['show', 1],
            ])
            ->get()->all();
        $menus = Menu::with('translations')->where([
            ['status', 1],
            ['show', 1]
        ])->get()->all();
        function getVaccancy($code)
        {
            static $data = null;
            if ($data == null) {
                $data = Blog::with('translations')->whereTranslation("title", $code)->first();
            }
            return $data;
        }
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
        return Inertia::render('SingleVaccancy', [
            "menus" => $menu,
            "vaccancy" => getVaccancy($code),
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


    public function blog(Request $request)
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


        return Inertia::render('Blog', [
            "links" => asset('storage/images/slider2logo'),
            'success' => Session::get('success'),
            "blogs" => Blog::with('translations', 'file')->orderBy('created_at', 'desc')->where(
                [
                    ['publish_end', '>=', date('Y-m-d')],
                    ['publish_start', '<=', date('Y-m-d')],
                ]
            )->limit(6)->get(),
            "UpcomingEvent" => UpcomingEvent::with(['file', 'translations'])->get(),
            "slider2" => Slider2::with('translations', 'file')->get(),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,
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

    public function singleblog(Request $request, $locale, $code)
    {
        function similarBlogs()
        {
            static $data = null;
            if ($data == null) {
                $data = Blog::whereHas('file', function ($innerQuery) {
                    $innerQuery->where([
                        ['path', '!=', null]
                    ]);
                })->with('translations', 'file')->inRandomOrder()->limit(6)->get();
            }
            return $data;
        }

        // dd($similarBlogs);
        $menu = Menu::with('translation')
            ->whereTranslation("visible", 1)
            ->where([
                ['status', 1],
                ['show', 1],
            ])
            ->get()->all();
        $menus = Menu::with('translations')->where([
            ['status', 1],
            ['show', 1]
        ])->get()->all();
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


        return Inertia::render('SingleBlog', [
            'menus' => $menu,
            'success' => Session::get('success'),
            "similarBlogs" => similarBlogs(),
            "blogs" => Blog::with('translations', 'file')->orderBy('created_at', 'desc')->find($code),
            "UpcomingEvent" => UpcomingEvent::with(['file', 'translations'])->get(),
            "slider2" => Slider2::with('translations', 'file')->get(),
            "sliders" => $sliders->get(), "page" => $page, "seo" => [
                "title" => $page->meta_title,
                "description" => $page->meta_description,
                "keywords" => $page->meta_keyword,
                "og_title" => $page->meta_og_title,
                "og_description" => $page->meta_og_description,
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
