<?php

// namespace App\Repositories\Frontend\Eloquent;

use App\Http\Request\LoginFrontRequest;
use App\Http\Request\LoginRequest;
use App\Http\Request\RegisterRequest;
use App\Models\Category;
use App\Models\Language;
use App\Models\User;
use App\Repositories\Frontend\AuthRepositoryInterface;
// use App\Repositories\Frontend\Eloquent\Base\BaseRepository;
use App\Repositories\Eloquent\Base\BaseRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthRepository extends BaseRepository
{

    public function __construct(User $model)
    {
        parent::__construct($model);
    }


    public function view()
    {
        if (Auth::user()) {
            //return redirect()->route('welcome', app()->getLocale());
            return view('auth.login');
        } else {
            return view('auth.login');
        }
    }

    /**
     * Authenticate login user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginFrontRequest $request)
    {
        if (!Auth::attempt([
            'email' => $request->loginEmail,
            'password' => $request->loginPassword
        ])) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'auth' => [__('validation.wrong_email_or_password')],
            ]);
            throw $error;
        }

        if (Auth::user()->status == 0) {
            Auth::logout();
            return redirect()->route('login-view', app()->getLocale());
        }

        return redirect()->route('welcome', app()->getLocale());
    }

    /**
     * Create Feature item into db.
     *
     * @param string $lang
     * @param array $request
     * @return bool
     */
    public function register(string $lang, RegisterRequest $request)
    {

        try {
            DB::beginTransaction();

            $model = $this->model->create([
                'name' => $request['first_name'] . ' ' . $request['last_name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'status' => 1
            ]);
            //dd($model);

            $localization = $this->getLocalization($lang);
            $model->profile()->create([
                'language_id' => $localization->id,
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'country' => $request['country']
            ]);


            $token = Str::random(40);
            $model->roles()->attach('2');

            $model->tokens()->create([
                'token' => Hash::make($token),
                'validate_till' => Carbon::now()->addDays(1)
            ]);
            DB::commit();
            Auth::login($model);
            return true;
        } catch (QueryException $exception) {
            //dd($exception->getMessage());
            DB::rollBack();
            return false;
        }
    }

    //    /**
    //     * Logout user.
    //     *
    //     * @param \Illuminate\Http\Request $request
    //     *
    //     * @return \Illuminate\Http\Response
    //     */
    //    public function logout(Request $request)
    //    {
    //
    //        if (Auth::user()) {
    //            Auth::logout();
    //
    //            $request->session()->invalidate();
    //            $request->session()->regenerateToken();
    //        }
    //        return redirect()->route('login-view', app()->getLocale());
    //    }

    protected function getLocalization(string $lang)
    {
        $localization = Language::where('abbreviation', $lang)->first();
        if (!$localization) {
            throw new Exception('Localization not exist.');
        }

        return $localization;
    }
}
