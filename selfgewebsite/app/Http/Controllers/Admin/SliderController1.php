<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SliderRequest1;
use Illuminate\Http\Request;
use App\Models\Slider2;
use App\Models\Menu;
use App\Repositories\SliderRepositoryInterface;
use App\Repositories\Eloquent\SliderRepository1;
use Illuminate\Support\Arr;

class SliderController1 extends Controller
{
    /**
     * @var \App\Repositories\SliderRepositoryInterface
     */
    private $slideRepository;

    /**
     * @param \App\Repositories\SliderRepositoryInterface $slideRepository
     */
    public function __construct(
        SliderRepository1 $slideRepository
    ) {
        $this->slideRepository = $slideRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(SliderRequest1 $request)
    {
        return view('admin.nowa.views.slider1.index', [
            'sliders' => $this->slideRepository->getData($request, ['translations']),
        ]);
    }

    public function create()
    {
        $slider = $this->slideRepository->model;

        $url = locale_route('slider1.store', [], false);
        $method = 'POST';
        function getMenu()
        {
            $data = null;
            if ($data == null) {
                $data = Menu::with("translations")->get()->all();
            }
            return $data;
        }

        return view('admin.nowa.views.slider1.form', [
            'slider' => $slider,
            "menus" => getMenu(),
            "links" => asset('storage/images/slider2logo'),
            'url' => $url,
            'method' => $method,
            "slide" => null
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        // $saveData['logo'] = $request->logo ? $name = $request->file('logo')->getClientOriginalName() : null;
        $slider = $this->slideRepository->create($saveData);

        // Save Files
        if ($request->hasFile('images')) {
            // dd($request->file('images'));
            $slider = $this->slideRepository->saveFiles($slider->id, $request);
        }

        return redirect(locale_route('slider1.index', $slider->id))->with('success', __('admin.create_successfully'));
    }

    public function show(string $locale, Slider $slider)
    {
        return view('admin.pages.slider.show', [
            'slider' => $slider,
        ]);
    }

    public function edit(string $locale, Slider2 $slider, $code)
    {
        $url = locale_route('slider1.update', $code, false);
        $method = 'PUT';
        function getMenu()
        {
            $data = null;
            if ($data == null) {
                $data = Menu::with("translations")->get()->all();
            }
            return $data;
        }

        return view('admin.nowa.views.slider1.form', [
            // 'slider' => $slider,
            'url' => $url,
            'menus' => getMenu(),
            // "slide" => $slider->find($code),
            "slider" => $slider->find($code),
            "links" => asset('storage/images/slider2logo'),
            'method' => $method,
        ]);
    }


    public function update(SliderRequest1 $request, string $locale, Slider2 $slider, $code)
    {
        // dd($request->file(), 'esaa', $this->slideRepository->model);
        if ($request->logo) {
            $name = $request->file('logo')->getClientOriginalName();
            $request->file('logo')->storeAs('public/images/slider2logo/', $name);
        }
        $saveData = Arr::except($request->except('_token'), []);
        // dd($saveData);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        $this->slideRepository->update($code, $saveData);

        $this->slideRepository->saveFiles($code, $request);


        return redirect(locale_route('slider1.index', $code))->with('success', __('admin.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $locale
     * @param \App\Models\Category $category
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(string $locale, Slider2 $slider, $code)
    {
        if (!$this->slideRepository->delete($code)) {
            return redirect(locale_route('slider.show', $code))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('slider1.index'))->with('success', __('admin.delete_message'));
    }
}
