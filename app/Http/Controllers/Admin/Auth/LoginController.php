<?php
/**
 *  app/Http/Controllers/Admin/Auth/AuthController.php
 *
 * Date-Time: 03.06.21
 * Time: 15:23
 * @author Insite LLC <hello@insite.international>
 */

namespace App\Http\Controllers\Admin\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;

/**
 * Class AuthController
 * @package App\Http\Controllers\Admin\Auth
 */
class LoginController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function loginView()
    {
        $pageConfigs = ['bodyCustomClass' => 'login-bg', 'isCustomizer' => false];

        //return view('admin.auth.login', []);

        return view('admin.nowa.views.login.signin');
    }

    /**
     * Authenticate login user.
     *
     * @param LoginRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function login(LoginRequest $request)
    {
        if (!\Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ],$request->remember)) {
             return back()->with('danger','Email or Password is incorrect!');
        }

        return redirect('/ge/adminpanel/page');
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        \Auth::logout();
        return redirect(locale_route('loginView'));
    }
}
