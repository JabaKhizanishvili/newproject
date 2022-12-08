<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\ContactEmail;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class ContactController extends Controller
{
    public function index()
    {
        $page = Page::where('key', 'contact')->firstOrFail();

        $images = [];
        foreach ($page->sections as $sections) {
            if ($sections->file) {
                $images[] = asset($sections->file->getFileUrlAttribute());
            } else {
                $images[] = null;
            }
        }


        return Inertia::render('Contact', ["page" => $page, "seo" => [
            "title" => $page->meta_title,
            "description" => $page->meta_description,
            "keywords" => $page->meta_keyword,
            "og_title" => $page->meta_og_title,
            "og_description" => $page->meta_og_description,
            //            "image" => "imgg",
            //            "locale" => App::getLocale()
        ], 'images' => $images])->withViewData([
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keyword' => $page->meta_keyword,
            "image" => $page->file,
            'og_title' => $page->meta_og_title,
            'og_description' => $page->meta_og_description
        ]);
    }

    public function mail(Request $request)
    {

        if ($request->method() == 'POST') {

            // dd($request->all());

            $validate = $request->validate([
                'name' => 'required|string|max:55',
                'lastname' => 'required|string|max:55',
                'mail' => 'required|email',
                'phone' => 'required',
                'message' => 'required|max:1024'
            ]);

            $data = [
                'name' => $request->name,
                'lastname' => $request->mail,
                'mail' => $request->phone,
                "phone" => $request->phone,
                'message' => $request->message
            ];
            dd($data);
            $mailTo = Setting::where(['key' => 'email'])->first();
            if (($mailTo !== null) && $mailTo->value) {
                Mail::to($mailTo->value)->send(new ContactEmail($data));
            }
        }


        // return Inertia::render('Home/Home');
        return redirect(route('client.home.index'));
    }


    public function ContactMail(Request $request)
    {

        if ($request->method() == 'POST') {

            // dd($request->all());

            $request->validate([
                'name' => 'required|string|max:55',
                'lastname' => 'required|string|max:55',
                'mail' => 'required|email',
                'phone' => 'required',
                'message' => 'required|max:1024'
            ]);

            // dd($request->all());

            $data = [
                'name' => $request->name,
                'lastname' => $request->mail,
                'mail' => $request->phone,
                "phone" => $request->phone,
                'message' => $request->message
            ];
            dd($data);
            $mailTo = Setting::where(['key' => 'email'])->first();
            if (($mailTo !== null) && $mailTo->value) {
                Mail::to($mailTo->value)->send(new ContactEmail($data));
            }
        }


        // return Inertia::render('Home/Home');
        return redirect(route('client.home.index'));
    }
}
