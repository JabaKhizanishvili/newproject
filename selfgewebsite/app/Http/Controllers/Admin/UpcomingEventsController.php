<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpcomingEventsRequest as SliderRequest;
// use App\Models\Slider as UpcomingEvent;
use App\Models\UpcomingEvent;
// use App\Repositories\SliderRepositoryInterface;
use App\Repositories\UpcomingEventsRepositoryInterface as SliderRepositoryInterface;
use App\Repositories\Eloquent\UpcomingEventsRepository;
use Illuminate\Support\Arr;

class UpcomingEventsController extends Controller
{
    /**
     * @var \App\Repositories\SliderRepositoryInterface
     */
    private $slideRepository;

    /**
     * @param \App\Repositories\SliderRepositoryInterface $slideRepository
     */
    public function __construct(
        UpcomingEventsRepository $slideRepository
    ) {
        $this->slideRepository = $slideRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(SliderRequest $request)
    {
        /*return view('admin.pages.slider.index', [
            'sliders' => $this->slideRepository->getData($request, ['translations'])
        ]);*/

        return view('admin.nowa.views.upcomingevents.index', [
            'sliders' => $this->slideRepository->getData($request, ['translations'])
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

        $url = locale_route('upcomingevents.store', [], false);
        $method = 'POST';

        /*return view('admin.pages.slider.form', [
            'slider' => $slider,
            'url' => $url,
            'method' => $method,
        ]);*/

        return view('admin.nowa.views.upcomingevents.form', [
            'slider' => $slider,
            'url' => $url,
            'method' => $method,
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
    public function store(SliderRequest $request)
    {
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];
        $slider = $this->slideRepository->create($saveData);

        // Save Files
        if ($request->hasFile('images')) {
            $slider = $this->slideRepository->saveFiles($slider->id, $request);
        }

        return redirect(locale_route('upcomingevents.index', $slider->id))->with('success', __('admin.create_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param string $locale
     * @param \App\Models\Product $product
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(string $locale, UpcomingEvent $slider)
    {
        return view('admin.pages.upcomingevents.show', [
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
    public function edit(string $locale, UpcomingEvent $event, $code)
    {
        $url = locale_route('upcomingevents.update', $code, false);
        $method = 'PUT';

        return view('admin.nowa.views.upcomingevents.form', [
            'slider' => $event->find($code),
            'url' => $url,
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
    public function update(SliderRequest $request, string $locale, UpcomingEvent $slider, $code)
    {
        // dd('asdasd');
        $saveData = Arr::except($request->except('_token'), []);
        $saveData['status'] = isset($saveData['status']) && (bool)$saveData['status'];

        $this->slideRepository->update($code, $saveData);

        $this->slideRepository->saveFiles($code, $request);


        return redirect(locale_route('upcomingevents.index', $code))->with('success', __('admin.update_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $locale
     * @param \App\Models\Category $category
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(string $locale,  $slider)
    {
        // dd($slider);
        if (!$this->slideRepository->delete($slider)) {
            return redirect(locale_route('upcomingevents.show', $slider))->with('danger', __('admin.not_delete_message'));
        }
        return redirect(locale_route('upcomingevents.index'))->with('success', __('admin.delete_message'));
    }
}
