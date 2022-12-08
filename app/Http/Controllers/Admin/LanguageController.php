<?php

/**
 *  app/Http/Controllers/Admin/LanguageController.php
 *
 * Date-Time: 03.06.21
 * Time: 16:15
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LanguageRequest;
use App\Models\Language;
use App\Repositories\LanguageRepositoryInterface;

/**
 * Class LanguageController
 * @package App\Http\Controllers\Admin
 */
class LanguageController extends Controller
{

    /**
     * @var \App\Repositories\LanguageRepositoryInterface
     */
    private $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        // Initialize languageRepository
        $this->languageRepository = $languageRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index(LanguageRequest $request)
    {
        /*return view('admin.pages.language.index', [
            'languages' => $this->languageRepository->getData($request)
        ]);*/

        return view('admin.nowa.views.language.index', [
            'languages' => $this->languageRepository->getData($request)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        $language = $this->languageRepository->model;

        $url = locale_route('language.store', [], false);
        $method = 'POST';

        /*return view('admin.pages.language.form', [
            'language' => $language,
            'url' => $url,
            'method' => $method
        ]);*/

        return view('admin.nowa.views.language.form', [
            'language' => $language,
            'url' => $url,
            'method' => $method
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Admin\LanguageRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(LanguageRequest $request)
    {
        $attributes = [
            'title' => $request['title'],
            'locale' => $request['locale'],
            'status' => (bool)$request['status']
        ];

        $this->languageRepository->create($attributes);

        return redirect(locale_route('language.index'))->with('success', 'Language Created.');
    }

    /**
     * Display the specified resource.
     *
     * @param string $locale
     * @param \App\Models\Language $language
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(string $locale, Language $language)
    {
        return view('admin.pages.language.show', [
            'language' => $language
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $locale
     * @param \App\Models\Language $language
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(string $locale, Language $language)
    {
        $url = locale_route('language.update', $language->id, false);
        $method = 'PUT';

        /*return view('admin.pages.language.form', [
            'language' => $language,
            'url' => $url,
            'method' => $method
        ]);*/

        return view('admin.nowa.views..language.form', [
            'language' => $language,
            'url' => $url,
            'method' => $method
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $locale
     * @param int $id
     *
     * @param \App\Http\Requests\Admin\LanguageRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(string $locale, Language $language, LanguageRequest $request)
    {
        $attributes = [
            'title' => $request['title'],
            'locale' => $request['locale'],
            'status' => $language->default || (bool)$request['status']
        ];
        $this->languageRepository->update($language->id, $attributes);

        return redirect(locale_route('language.index', $language->id))->with('success', 'Language Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $locale
     * @param \App\Models\Language $language
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(string $locale, Language $language)
    {
        if ($language->default) {
            return redirect(locale_route('language.index'))->with('danger', 'Can not delete default language.');
        }
        if (!$this->languageRepository->delete($language->id)) {
            return redirect(locale_route('language.show', $language->id))->with('danger', 'Language not deleted.');
        }
        return redirect(locale_route('language.index'))->with('success', 'Language Deleted.');
    }
}
