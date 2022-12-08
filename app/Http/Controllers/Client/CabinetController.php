<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Repositories\Eloquent\GalleryRepository;

class CabinetController extends Controller
{
    protected $galleryRepository;

    public function __construct(GalleryRepository $galleryRepository)
    {
        $this->galleryRepository = $galleryRepository;
        //auth()->guard('customer')->logout();
        //request()->session()->invalidate();

        //request()->session()->regenerateToken();
    }

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


        $files = [];
        if ($page->images) $files = $page->files;

        $user = Auth::guard('customer')->user();


        //$docs = $user->docs;

        $docs = $user->subclasses()->with(['translation', 'docs'])->get();

        $result = [];
        $k = 0;
        foreach ($docs as $doc) {
            $result[$doc->class_id]['title'] = $doc->class->title;
            $result[$doc->class_id]['subclasses'][] = $doc;
            $k++;
        }
        $result = array_values($result);
        //dd($result);

        return Inertia::render('Cabinet/Cabinet', ["page" => $page, 'docs' => $result, "seo" => [
            "title" => $page->meta_title,
            "description" => $page->meta_description,
            "keywords" => $page->meta_keyword,
            "og_title" => $page->meta_og_title,
            "og_description" => $page->meta_og_description,
            //            "image" => "imgg",
            //            "locale" => App::getLocale()
        ], 'gallery_img' => $files, 'images' => $images])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }
}
