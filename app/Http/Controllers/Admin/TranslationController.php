<?php

/**
 *  app/Http/Controllers/Admin/TranslationController.php
 *
 * Date-Time: 07.06.21
 * Time: 09:35
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TranslationRequest;
use App\Models\Language;
use App\Models\LanguageLine;
use App\Repositories\TranslationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Class TranslationController
 * @package App\Http\Controllers\Admin
 */
class TranslationController extends Controller
{

    /**
     * @var \App\Repositories\TranslationRepositoryInterface
     */
    private $translationRepository;

    public function __construct(TranslationRepositoryInterface $translationRepository)
    {
        // Initialize TranslationRepository
        $this->translationRepository = $translationRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(TranslationRequest $request)
    {
        /*return view('admin.pages.translation.index', [
            'translations' => $this->translationRepository->getData($request),
            'languages' => $this->activeLanguages()
        ]);*/

        return view('admin.nowa.views.translation.index', [
            'translations' => $this->translationRepository->getData($request),
            'languages' => $this->activeLanguages()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string $locale
     * @param int $id
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(string $locale, int $id)
    {
        return view('admin.pages.translation.show', [
            'translation' => $this->translationRepository->findOrFail($id),
            'languages' => $this->activeLanguages()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $locale
     * @param int $id
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(string $locale, int $id)
    {
        $url = locale_route('translation.update', $id, false);
        $method = 'PUT';

        /*return view('admin.pages.translation.form', [
            'translation' => $this->translationRepository->findOrFail($id),
            'url' => $url,
            'method' => $method,
            'languages' => $this->activeLanguages()
        ]);*/

        return view('admin.nowa.views.translation.form', [
            'translation' => $this->translationRepository->findOrFail($id),
            'url' => $url,
            'method' => $method,
            'languages' => $this->activeLanguages()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $locale
     * @param int $id
     *
     * @param \App\Http\Requests\Admin\TranslationRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(string $locale, int $id, TranslationRequest $request)
    {
        $data = $request->only('text');
        $this->translationRepository->update($id, $data);

        // Clear cache
        Artisan::call('cache:clear');

        return ['msg' => 'success', 'status' => 'ok'];

        //return redirect(locale_route('translation.index', $id))->with('success', 'Translation Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
