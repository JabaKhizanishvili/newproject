<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;
use App\Rules\MatchOldPassword;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    /**
     * @var SettingRepositoryInterface
     */


    /**
     * @param SettingRepositoryInterface $settingRepository
     */
    public function __construct()
    {
    }


    /**
     * @param SettingRequest $request
     * @return Application|Factory|View
     */
    public function index()
    {
        /*return view('admin.pages.setting.index', [
            'settings' => $this->settingRepository->getData($request, ['translations'])
        ]);*/

        return view('admin.nowa.views.password.index', []);
    }


    /**
     * @param string $locale
     * @param Setting $setting
     * @return Application|Factory|View
     */
    public function show(string $locale, Setting $setting)
    {
        return view('admin.pages.setting.show', [
            'setting' => $setting,
        ]);
    }


    /**
     * @param string $locale
     * @param Setting $setting
     * @return Application|Factory|View
     */
    public function edit(string $locale, Setting $setting)
    {
        $url = locale_route('setting.update', $setting->id, false);
        $method = 'PUT';

        /*return view('admin.pages.setting.form', [
            'setting' => $setting,
            'url' => $url,
            'method' => $method,
        ]);*/

        return view('admin.nowa.views.setting.form', [
            'setting' => $setting,
            'url' => $url,
            'method' => $method,
        ]);
    }


    /**
     * @param SettingRequest $request
     * @param string $locale
     * @param Setting $setting
     * @return Application|RedirectResponse|Redirector
     */
    public function update(\Illuminate\Http\Request $request)
    {

        $request->validate([
            'c_pass' => ['required', new MatchOldPassword()],
            'n_pass' => ['required', 'min:5'],
            'r_pass' => ['same:n_pass']
        ]);
        //dd($request->all());

        User::find(auth()->user()->id)->update(['password' => Hash::make($request->n_pass)]);


        return redirect(locale_route('password.index'))->with('success', __('admin.update_successfully'));
    }
}
