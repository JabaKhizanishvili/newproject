<?php

namespace App\Http\Controllers;

use App\Models\Iconimage;
use App\Models\Blog;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\PageSection;
use App\Repositories\Eloquent\Base\BaseRepository;
use App\Repositories\PageSectionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IconController extends Controller
{
    //

    public function addImage(Request $request, Iconimage $img)
    {

        if ($request->blog_id == null) {
            $request->blog_id = Blog::orderBy('id', 'DESC')->first()->id + 1;
        }

        if ($request->file('header_icon') != null) {
            $name = $request->file('header_icon')->getClientOriginalName();
            $size = $request->file('header_icon')->getSize();
            if (Storage::exists('public/images/blogIcons/' . $name)) {
                Storage::delete('public/images/blogIcons/' . $name);
            }
            $request->file('header_icon')->storeAs('public/images/blogIcons', $name);
            $img->create([
                "name" => $name,
                "type" => 'header_icon',
                "size" => $size,
                "blog_id" => $request->blog_id,
            ]);
            if (!$img) {
                dd(false);
            }
        } elseif ($request->file('header_icon') == null) {

            if ($request->header_iconcheck != null) {
            } else {
                Iconimage::where([
                    ['blog_id', $request->blog_id],
                    ['type', 'header_icon'],
                ])->delete();
            }
        }

        if ($request->file('icon1') != null) {
            $name = $request->file('icon1')->getClientOriginalName();
            $size = $request->file('icon1')->getSize();
            // if (Storage::exists('public/images/blogIcons/' . $name)) {
            //     Storage::delete('public/images/blogIcons/' . $name);
            // }
            // dd($request->file('icon1'));
            $request->file('icon1')->storeAs('public/images/blogIcons', $name);
            $img->create([
                "name" => $name,
                "type" => 'icon1',
                "size" => $size,
                "blog_id" => $request->blog_id,
            ]);
            if (!$img) {
                dd(false);
            }
        } elseif ($request->file('icon1') == null) {
            if ($request->icon1check != null) {
            } else {
                Iconimage::where([
                    ['blog_id', $request->blog_id],
                    ['type', 'icon1'],
                ])->delete();
            }
        }
        if ($request->file('icon2') != null) {
            $name = $request->file('icon2')->getClientOriginalName();
            $size = $request->file('icon2')->getSize();
            // if (Storage::exists('public/images/blogIcons/' . $name)) {
            //     Storage::delete('public/images/blogIcons/' . $name);
            // }
            // dd($request->file('icon2'));
            $request->file('icon2')->storeAs('public/images/blogIcons', $name);
            $img->create([
                "name" => $name,
                "type" => 'icon2',
                "size" => $size,
                "blog_id" => $request->blog_id,
            ]);
            if (!$img) {
                dd(false);
            }
        } elseif ($request->file('icon2') == null) {
            if ($request->icon2check != null) {
            } else {
                Iconimage::where([
                    ['blog_id', $request->blog_id],
                    ['type', 'icon2'],
                ])->delete();
            }
        }

        if ($request->file('icon3') != null) {
            $name = $request->file('icon3')->getClientOriginalName();
            $size = $request->file('icon3')->getSize();
            // if (Storage::exists('public/images/blogIcons/' . $name)) {
            //     Storage::delete('public/images/blogIcons/' . $name);
            // }
            // dd($request->file('icon3'));
            $request->file('icon3')->storeAs('public/images/blogIcons', $name);
            $img->create([
                "name" => $name,
                "type" => 'icon3',
                "size" => $size,
                "blog_id" => $request->blog_id,
            ]);
            if (!$img) {
                dd(false);
            }
        } elseif ($request->file('icon3') == null) {
            if ($request->icon3check != null) {
            } else {
                Iconimage::where([
                    ['blog_id', $request->blog_id],
                    ['type', 'icon3'],
                ])->delete();
            }
        }
        return redirect()->back();
    }
}
