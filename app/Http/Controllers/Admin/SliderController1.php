<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SliderRequest1;
use Illuminate\Http\Request;
use App\Models\Slider2;
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
        /*return view('admin.pages.slider.index', [
            'sliders' => $this->slideRepository->getData($request, ['translations'])
        ]);*/
        return view('admin.nowa.views.slider1.index', [
            'sliders' => $this->slideRepository->getData($request, ['translations']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $slider = $this->slideRepository->model;

        $url = locale_route('slider1.store', [], false);
        $method = 'POST';

        /*return view('admin.pages.slider.form', [
            'slider' => $slider,
            'url' => $url,
            'method' => $method,
        ]);*/

        return view('admin.nowa.views.slider1.form', [
            'slider' => $slider,
            "links" => asset('storage/images/slider2logo'),
            'url' => $url,
            'method' => $method,
            "slide" => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\ProductRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \ReflectionException
     */
    public function store(Request $request)
    {
        // dd($request->all(), $request->logo);
        if ($request->logo) {
            $name = $request->file('logo')->getClientOriginalName();
            $request->file('logo')->storeAs('public/images/slider2logo/', $name);
        }
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        $saveData['logo'] = $request->logo ? $name = $request->file('logo')->getClientOriginalName() : null;
        // dd($saveData);
        $slider = $this->slideRepository->create($saveData);

        // Save Files
        if ($request->hasFile('images')) {
            $slider = $this->slideRepository->saveFiles($slider->id, $request);
        }

        return redirect(locale_route('slider1.index', $slider->id))->with('success', __('admin.create_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param string $locale
     * @param \App\Models\Product $product
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(string $locale, Slider $slider)
    {
        return view('admin.pages.slider.show', [
            'slider' => $slider,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $locale
     * @param \App\Models\Category $category
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(string $locale, Slider2 $slider, $code)
    {
        // dd($code);
        $url = locale_route('slider1.update', $code, false);
        $method = 'PUT';
        /*return view('admin.pages.slider.form', [
            'slider' => $slider,
            'url' => $url,
            'method' => $method,
        ]);*/

        return view('admin.nowa.views.slider1.edit', [
            'slider' => $slider,
            'url' => $url,
            "slide" => $slider->find($code),
            "links" => asset('storage/images/slider2logo'),
            'method' => $method,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Admin\CategoryRequest $request
     * @param string $locale
     * @param \App\Models\Category $category
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(SliderRequest1 $request, string $locale, Slider2 $slider, $code)
    {
        if ($request->logo) {
            $name = $request->file('logo')->getClientOriginalName();
            $request->file('logo')->storeAs('public/images/slider2logo/', $name);
        }
        $saveData = Arr::except($request->except('_token'), []);
        // dd($saveData);
        if ($request->logo == null) {
            $saveData = Arr::except($request->except('logo'), []);
        } else {
            $saveData['logo'] = $request->logo ? $request->file('logo')->getClientOriginalName() : null;
        }
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
