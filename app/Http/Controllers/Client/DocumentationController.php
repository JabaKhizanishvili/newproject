<?php

namespace App\Http\Controllers\Client;

use App\Models\Setting;
use App\Mail\ContactEmail;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Slider;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Http\Request;
use App\Models\Rateservice;
use App\Models\Owneraplication;
use App\Models\Ownerapplication;

class DocumentationController extends Controller
{
    public function index(Request $request)
    {


        $page = Page::where('key', 'owner-application')->firstOrFail();

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

        return Inertia::render('Documents/OwnerApplication', ['error' => $request->session()->get('error'), 'success' => $request->session()->get('success'), "sliders" => $sliders->get(), "page" => $page, "seo" => [
            "title" => $page->meta_title,
            "description" => $page->meta_description,
            "keywords" => $page->meta_keyword,
            "og_title" => $page->meta_og_title,
            "og_description" => $page->meta_og_description,
            "success" => $request->session()->get('success'),

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

    public function evaluation(Request $request)
    {


        $page = Page::where('key', 'evaluation')->firstOrFail();

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

        return Inertia::render('Documents/Evaluation', ['success' => $request->session()->get('success'), "sliders" => $sliders->get(), "page" => $page, "seo" => [
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

    public function circulars()
    {


        $page = Page::where('key', 'circulars')->firstOrFail();

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

        return Inertia::render('Documents/Circulars', ["sliders" => $sliders->get(), "page" => $page, "seo" => [
            "title" => $page->meta_title,
            "description" => $page->meta_description,
            "keywords" => $page->meta_keyword,
            "og_title" => $page->meta_og_title,
            "og_description" => $page->meta_og_description,

            //            "image" => "imgg",
            //            "locale" => App::getLocale()
        ], 'popular_products' => $products, 'images' => $images, 'docs' => $page->docs])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    // add rateservices to db
    public function add_rateservices(Request $request)
    {

        // dd($request->all());
        $rateservices = Rateservice::create(
            $request->only(
                'name',
                // 'created_at',
                'surname',
                'phone',
                'email',
                'course',
            )
        );


        /* if ($rateservices) {
            $mailTo = Setting::where(['key' => 'email'])->first();
            if (($mailTo !== null) && $mailTo->value) {
                Mail::to($mailTo->value)->send(new ContactEmail($rateservices));
            }
            return redirect(route('client.services.ownerapplication', app()->getLocale()))->with('success', 'warmatebit');
        } else {
            return redirect(route('client.services.ownerapplication', app()->getLocale()))->with('error', 'error');
        }*/

        return redirect()->back()->with('success', 'warmatebit');
        // dd($request->post());

    }

    // send owner application to db
    public function sendapplication(Request $request)
    {
        // validate input
        $request->validate([
            'name_of_ship' => ['required'],
            'ex_names' => ['required'],
            'imo_no' => ['required'],
            'type' => ['required'],
            'existing_flag' => ['required'],
            'call_sign' => ['required'],
            'grt' => ['required'],
            'nrt' => ['required'],
            'dwt' => ['required'],
            'date_keel_laid' => ['required'],
            'build' => ['required'],
            'framing_system' => ['required'],
            'builders' => ['required'],
            'loa' => ['required'],
            'lpp' => ['required'],
            'breath' => ['required'],
            'depth' => ['required'],
            'main_engine_builders' => ['required'],
            'type2' => ['required'],
            'bhp' => ['required'],
            'number_and_type_of_generators' => ['required'],
            'cargogear' => ['required'],
            'owning_company' => ['required'],
            'managing_company' => ['required'],
            'place_date' => ['required'],
            'name_of_applicant' => ['required'],
            'hull_machinery' => ['required'],
            'cargo_gear' => ['required'],
            'load_line' => ['required'],
            'safety_constructor' => ['required'],
            'safety_equipment' => ['required'],
            'passenger_safety' => ['required'],
            'safety_radio' => ['required'],
            'marpol_annexi' => ['required'],
            'marpol_annexii' => ['required'],
            'marpol_annexiii' => ['required'],
            'marpol_annexv' => ['required'],
            'marpol_annex_vi' => ['required'],
            'energy_efficiency' => ['required'],
            'engine_iopc' => ['required'],
            'fishing_vessel' => ['required'],
            'tonnage_certificate' => ['required'],
            'solid_bulk_cards' => ['required'],
            'dangerous_goods' => ['required'],
            'doc' => ['required'],
            'smc' => ['required'],
            'isps_on_board_verification' => ['required'],
            'mlc_2006' => ['required'],
            'ballast_water' => ['required'],
            'antifouling_survey' => ['required'],
            'exemption' => ['required'],
            'cargo_ship_safety_under_500_grt' => ['required'],
            'seaworthiness' => ['required'],
            'hsc_dsc_safety' => ['required'],
        ]);

        // add to db
        $ownwerapplciation = Ownerapplication::create(
            $request->only(
                'name_of_ship',
                'ex_names',
                'imo_no',
                'type',
                'existing_flag',
                'call_sign',
                'grt',
                'nrt',
                'dwt',
                'date_keel_laid',
                'build',
                'framing_system',
                'builders',
                'loa',
                'lpp',
                'breath',
                'depth',
                'main_engine_builders',
                'type2',
                'bhp',
                'number_and_type_of_generators',
                'cargogear',
                'owning_company',
                'managing_company',
                'place_date',
                'name_of_applicant',
                'hull_machinery',
                'cargo_gear',
                'load_line',
                'safety_constructor',
                'safety_equipment',
                'passenger_safety',
                'safety_radio',
                'marpol_annexi',
                'marpol_annexii',
                'marpol_annexiii',
                'marpol_annexv',
                'marpol_annex_vi',
                'energy_efficiency',
                'engine_iopc',
                'fishing_vessel',
                'tonnage_certificate',
                'solid_bulk_cards',
                'dangerous_goods',
                'doc',
                'smc',
                'isps_on_board_verification',
                'mlc_2006',
                'ballast_water',
                'antifouling_survey',
                'exemption',
                'cargo_ship_safety_under_500_grt',
                'seaworthiness',
                'hsc_dsc_safety',
                'manual_approval',
            )
        );

        // send mail

        $data = [
            'test' => 'asd'
        ];
        if ($ownwerapplciation) {
            $mailTo = Setting::where(['key' => 'email'])->first();
            if (($mailTo !== null) && $mailTo->value) {
                Mail::to($mailTo->value)->send(new ContactEmail($data));
            }
            return redirect(route('client.services.ownerapplication', app()->getLocale()))->with('success', 'warmatebit');
        } else {
            return redirect(route('client.services.ownerapplication', app()->getLocale()))->with('error', 'error');
        }
    }
}
