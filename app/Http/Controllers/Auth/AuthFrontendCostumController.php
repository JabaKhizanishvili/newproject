<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Request\LoginFrontRequest;
// use App\Http\Request\LoginRequest;
use App\Http\Request\RegisterRequest;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Repositories\Frontend\AuthRepositoryInterface;
// use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\AuthRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class AuthFrontendCostumController extends Controller
{
    // protected $authRepository;

    // public function __construct(AuthRepository $authRepository)
    // {
    //     $this->authRepository = $authRepository;
    // }


    //    public function __construct(AuthService $service)
    //    {
    //        $this->service = $service;
    //    }
    /**
     * Show specified view.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function loginView()
    {
        return  $this->authRepository->view();
    }

    /**
     * Authenticate login user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // return redirect()->intended('dashboard');
            return redirect(route("client.cabinet"));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }


    public function register(Request $request, string $lang, User $user)
    {
        // dd($request->post());
        // return redirect()->route('client.login');
        // return redirect()->route('client.home.index');
        // return redirect(route('client.home.index'))->with('message', 'TEST');


        // $request->validate([
        //     'first_name' => 'required|string|max:255',
        //     'last_name' => 'required|string|max:255',
        //     'phone' => 'required|string|max:255',
        //     'email' => 'required|string|max:255|unique:users',
        //     'password' => 'required|string|max:255',
        //     'password_repeat' => 'required|string|max:255|same:password',
        // ]);


        $registered = $user->create([
            'name' => $request['first_name'] . ' ' . $request['last_name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'status' => 1
        ]);


        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);


        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect(route("client.cabinet"));
        }

        // if ($registered) {
        //     return redirect(route("client.login"))->with('success', 'successfully registered');
        //     // dd('asds');
        // }
    }

    public function logout(Request $request)
    {

        if (Auth::user()) {
            Auth::logout();
        }
        return redirect()->route('loginViewFront', app()->getLocale());
    }

    public function verify($locale, $token)
    {
        $data = explode('|', $token);
        $user = User::findOrFail($data[0]);
        if ($user->status == 1 || Auth::user()) {
            return redirect()->route('welcome', app()->getLocale());
        }
        $tokens = $user->tokens()->where('validate_till', '>=', Carbon::now())->get();
        if (count($tokens) > 0) {
            foreach ($tokens as $item) {
                if (Hash::check($data[1], $item->token)) {
                    $user->status = 1;
                    $user->save();
                    break;
                }
            }
        } else {
            $user->delete();
        }
        return redirect()->route('welcome', app()->getLocale());
    }
}
