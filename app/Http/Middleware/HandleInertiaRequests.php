<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Repositories\Eloquent\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'client/base';

    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request)
    {
        $this->settings();
        $locales = config("translatable.locales");
        $currentRoute = url()->current();

        //$categories = $this->categoryRepository->getVisibleCategoryTree();



        //$result = $this->buildTree($categories);

        $info = Setting::with(['translation'])->get();

        //dd($info);
        $_result = [];
        foreach ($info as $item) {
            $_result[$item->key] = $item;
        }

        //dd($result,$categories);


        //dd(Auth::guard('customer')->user());
        //Generates urls for language switcher with each locale
        $locale_urls = $this->locale_urls();
        //Generates link for go back button
        $urlPrev = $this->urlPrev();
        return array_merge(parent::share($request), [
            "locales" => $locales,
            "pathname" => $currentRoute,
            "locale_urls" => $locale_urls,
            'urlPrev'    => $urlPrev,
            //'categories' => $result,
            'info' => $_result,
            'user' => Auth::guard('customer')->user(),
            'flash' => session('danger')
        ]);
    }

    private function buildTree($data)
    {
        $result = [];

        foreach ($data as $key => $category) {
            $result[$key]['title'] = $category->title;
            $result[$key]['id'] = $category->id;
            $result[$key]['slug'] = $category->slug;
            $result[$key]['status'] = $category->status;
            $result[$key]['position'] = $category->position;
            $result[$key]['children'] = [];
            $result[$key]['files'] = $category->files;
            if (count($category->children)) {
                $result[$key]['children'] = $this->buildTree($category->children);
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function urlPrev()
    {
        //Generates link for go back button
        if (url()->previous() !== route('login') && url()->previous() !== '' && url()->previous() !== url()->current()) {
            return url()->previous();
        } else {
            return "/";
        }
    }

    /**
     * @return array
     */
    protected function locale_urls()
    {
        $locales = config("translatable.locales");
        $routes = [];
        foreach ($locales as $key => $val) {
            $routes[$key] = get_url($val);
        }
        return $routes;
    }

    protected function settings()
    {
        $gphone = "";
        $gemail = "";
        $gaddress = "";
        $gtwitter = "";
        // $gcity = "";
        // $gcountry = "";
        $ginstagram = "";
        $gfacebook = "";
        $gyoutube = "";
        $gbehance = "";
        $gdribbble = "";
        $glinkedin = "";

        $settings = Setting::with(['translations'])->get();
        foreach ($settings as $setting) {
            switch ($setting->key) {
                case "phone":
                    $gphone = $setting;
                    break;
                case "email":
                    $gemail = $setting;
                    break;
                case "address":
                    $gaddress = $setting;
                    break;
                case "instagram":
                    $ginstagram = $setting;
                    break;
                case "facebook":
                    $gfacebook = $setting;
                    break;
                case "twitter":
                    $gtwitter = $setting;
                    break;
                case "youtube":
                    $gyoutube = $setting;
                    break;
                case "behance":
                    $gbehance = $setting;
                    break;
                case "dribbble":
                    $gdribbble = $setting;
                    break;
                case "linkedin":
                    $glinkedin = $setting;
                    break;
            }
        }

        Inertia::share([
            "gphone" => $gphone,
            "gemail" => $gemail,
            "gaddress" => $gaddress,
            "ginstagram" => $ginstagram,
            "gfacebook" => $gfacebook,
            "gbehance" => $gbehance,
            "gdribbble" => $gdribbble,
            "gtwitter" => $gtwitter,
            "gyoutube" => $gyoutube,
            "glinkedin" => $glinkedin,
        ]);
    }
}
