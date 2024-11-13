<?php

namespace App\Http\Controllers\Auth;

use Validator;
use Settings;
use Colors;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function authenticated(Request $request, $user)
    {
        $user->last_login = Carbon::now()->toDateTimeString();
        $user->save();
    }

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Prompt Login User
     * @return Redirect
     */
    public function prompt()
    {
        return view('auth.login')
            ->with('activeLoginMethods', Settings::getLoginMethods());
    }

    /**
     * Show Register User Page
     * @param  User   $user
     */
    public function showRegister($method)
    {
        if (!in_array($method, Settings::getLoginMethods())) {
            Session::flash('alert-danger', 'Login Method is not supported.');
            return Redirect::back();
        }
        switch ($method) {
            case 'steam':
                if (!Session::has('user')) {
                    return Redirect::to('/');
                }

                $user = Session::get('user');

                if (
                    is_null($user['steamid']) ||
                    is_null($user['steam_avatar']) ||
                    is_null($user['steamname'])
                ) {
                    return redirect('/'); // redirect to site
                }
                return view('auth.register', $user)->with('loginMethod', 'steam');
                break;
            default:
                return view('auth.register')->with('loginMethod', 'standard');
                break;
        }
    }

    /**
     * Register User Page
     * @param  User   $user
     * @param  Request   $request
     * @return Redirect
     */
    public function register($method, Request $request, User $user)
    {
        if (!in_array($method, Settings::getLoginMethods())) {
            Session::flash('alert-danger', 'Login Method is not supported.');
            return Redirect::back();
        }
        if (isset($request->url) && $request->url != '') {
            return Redirect::back();
        }
        switch ($method) {
            case 'steam':
                $validationRules = [
                    'firstname' => 'required|string',
                    'surname'   => 'required|string',
                    'steamid'   => 'required|string',
                    'avatar'    => 'required|string',
                    'steamname' => 'required|string',
                    'username'  => 'required|unique:users,username',
                ];

                if (Settings::isAuthSteamRequireEmailEnabled()) {
                    $validationRules['email'] = 'required|filled|email|unique:users,email';
                }

                if (Settings::isAuthRequirePhonenumberEnabled()) {
                    $validationRules['phonenumber'] = 'required|filled|phone:AUTO,DE';
                }

                $this->validate($request, $validationRules);

                $user->steam_avatar         = $request->avatar;
                $user->selected_avatar      = 'steam';
                $user->steamid              = $request->steamid;
                $user->steamname            = $request->steamname;

                if (Settings::isAuthSteamRequireEmailEnabled()) {
                    $user->email          = $request->email;
                } else {
                    $user->email_verified_at    = new \DateTime('NOW');
                }

                if (Settings::isAuthRequirePhonenumberEnabled()) {
                    $user->phonenumber          = $request->phonenumber;
                }

                break;

            default:
                $rules = [
                    'email'         => 'required|filled|email|unique:users,email',
                    'password1'     => 'required|same:password2|min:8',
                    'username'      => 'required|unique:users,username',
                    'firstname'     => 'required|string',
                    'surname'       => 'required|string',
                ];

                $messages = [
                    'username.unique'       => 'Username must be unique',
                    'username.required'     => 'Username is required',
                    'email.filled'          => 'Email Cannot be blank.',
                    'email.required'        => 'Email is required.',
                    'email.email'           => 'Email must be a valid Email Address.',
                    'email.unique'          => 'Email must be unique.',
                    'password1.same'        => 'Passwords must be the same.',
                    'password1.required'    => 'Password is required.',
                    'password1.min'         => 'Password must be atleast 8 characters long.',
                ];

                if (Settings::isAuthRequirePhonenumberEnabled()) {
                    $rules['phonenumber'] = 'required|filled|phone:AUTO,DE';
                    $messages['phonenumber.phone'] = 'The field contains an invalid number.';
                }

                $this->validate($request, $rules, $messages);
                $user->email          = $request->email;
                $user->password       = Hash::make($request->password1);
                $user->selected_avatar  = 'local';
                break;
        }

        $user->firstname        = $request->firstname;
        $user->surname          = $request->surname;
        $user->username         = $request->username;
        $user->username_nice    = strtolower(str_replace(' ', '-', $request->username));
        $user->locale           = Settings::getSiteLocale();


        if (!$user->save()) {
            Auth::logout();
            return Redirect('/')->with('error', 'Something went wrong. Please Try again later');
        }
        Session::forget('user');
        Auth::login($user, true);
        if ($method == 'standard') {
            $user->sendEmailVerificationNotification();
        }
        return Redirect('/account');
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        //notice we are not doing any validation, you should do it

        $user = Socialite::driver($provider)->user();

        // stroing data to our use table and logging them in
        $data = [
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];

        Auth::login(User::firstOrCreate($data));

        //after login redirecting to home page
        return redirect($this->redirectPath());
    }
}
