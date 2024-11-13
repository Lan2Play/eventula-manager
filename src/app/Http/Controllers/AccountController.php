<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Settings;

use App\Rules\ValidLocale;

class AccountController extends Controller
{
    /**
     * Show Account Index Page
     * @return View
     */
    public function index()
    {
        $user = Auth::user();
        $creditLogs = false;
        if (Settings::isCreditEnabled()) {
            $creditLogs = $user->creditLogs()->paginate(5, ['*'], 'cl');
        }
        $purchases = $user->purchases()->paginate(5, ['*'], 'pu');
        $tickets = $user->eventParticipants()
        ->orderBy('created_at', 'desc')
        ->paginate(5, ['*'], 'ti');
        return view("accounts.index")
            ->with('user', $user)
            ->with('creditLogs', $creditLogs)
            ->with('purchases', $purchases)
            ->with('eventParticipants', $tickets);
    }

    /**
     * Show Email Change Page
     * @return View
     */
    public function showMail()
    {
        $user = Auth::user();

        return view("accounts.email")
            ->with('user', $user);
    }

    /**
     * Show Remove single sign on Page
     * @return View
     */
    public function showRemoveSso($method)
    {
        $user = Auth::user();

        return view("accounts.removesso")
            ->with('user', $user)
            ->with('method', $method);
    }


    /**
     * add a user token
     * @return View
     */
    public function addToken(Request $request)
    {
        $rules = [
            'token_name'         => 'filled|string',
        ];
        $messages = [
            'token_name.filled'      => 'Token Name Cannot be blank.',
        ];
        $this->validate($request, $rules, $messages);


        foreach ($request->user()->tokens as $currtoken) {
            if ($request->token_name == $currtoken->name) {
                Session::flash('alert-danger', "This Token name is already in use!");
                return Redirect::back();
            }
        }

        $token = $request->user()->createToken($request->token_name);

        Session::flash('alert-success', "The Token is created successfully! You can find it above!. Note: it is only shown a single time, so keep it safe!");
        return redirect::back()->with('newtoken', $token->plainTextToken);
    }

    /**
     * remove a user token
     * @return View
     */
    public function removeToken($token)
    {
        $user = Auth::user();
        if ($token == null || $token == "") {
            Session::flash('alert-danger', "Token id is not available!");
            return Redirect::back();
        }

        $selectedtoken = false;

        foreach ($user->tokens as $currtoken) {
            if ($token == $currtoken->id) {
                $selectedtoken = $currtoken;
            }
        }

        if ($selectedtoken == false) {
            Session::flash('alert-danger', "This Token could not be found on your user!");
            return Redirect::back();
        }

        if (!$selectedtoken->delete()) {
            Session::flash('alert-danger',  "This Token could not be deleted!");
            return Redirect::back();
        }



        Session::flash('alert-success', "Token deleted successfully!");
        return redirect('/account');
    }

    /**
     * start the application authentication wizzard
     * @return View
     */
    public function showTokenWizzardStart($application = "", $callbackurl = "")
    {
        $user = Auth::user();
        if ($application == null || $application == "") {
            return view("accounts.tokenwizzard_start")->with('status', 'no_application');
        }


        foreach ($user->tokens as $currtoken) {
            if ($currtoken->name == $application) {
                return view("accounts.tokenwizzard_start")->with('status', 'exists')->with('application', $application)->with('callbackurl', $callbackurl);
            }
        }

        return view("accounts.tokenwizzard_start")->withStatus("not_exists")->with('application', $application)->with('callbackurl', $callbackurl);
    }

    /**
     * finish the application authentication wizzard
     * @return View
     */
    public function showTokenWizzardFinish(Request $request)
    {
        $user = Auth::user();


        foreach ($user->tokens as $currtoken) {
            if ($currtoken->name == $request->application) {
                if (!$currtoken->delete()) {
                    return view("accounts.tokenwizzard_finish")->with('status', 'del_failed')->with('application', $request->application);
                }
            }
        }



        $token = $user->createToken($request->application);

        if ($token->plainTextToken == null || $token->plainTextToken == "") {
            return view("accounts.tokenwizzard_finish")->with('status', 'creation_failed')->with('application', $request->application);
        }

        $newcallbackurl = $request->callbackurl . "://" . $token->plainTextToken;

        return view("accounts.tokenwizzard_finish")->with('status', 'success')->with('newtoken', $token->plainTextToken)->with('application', $request->application)->with('callbackurl', $newcallbackurl);
    }


    /**
     * add single sign on
     * @return View
     */
    public function addSso($method)
    {
        switch ($method) {
            case 'steam':
                return redirect('/login/steam');
                break;
            default:
                return Redirect::back()->with('error', 'no valid sso method selected');
                break;
        }
    }

    /**
     * remove single sign on
     * @return View
     */
    public function removeSso(Request $request, $method)
    {
        $user = Auth::user();
        $mailchanged = false;


        if ($user->email != $request->email) {
            $rules = [
                'email'         => 'filled|email|unique:users,email',
            ];
            $messages = [
                'email.filled'      => 'Email Cannot be blank.',
                'email.unique'      => 'Email is already in use.',
            ];
            $this->validate($request, $rules, $messages);

            $user->email_verified_at = null;

            $user->email = @$request->email;
            $mailchanged = true;
        }

        if (isset($request->password1) && $request->password1 != null) {
            $rules = [
                'password1'     => 'same:password2|min:8',
                'password2'     => 'same:password1|min:8',
            ];
            $messages = [
                'password1.same'    => 'Passwords must be the same.',
                'password1.min'     => 'Password must be atleast 8 characters long.',
                'password2.same'    => 'Passwords must be the same.',
                'password2.min'     => 'Password must be atleast 8 characters long.',
            ];
            $this->validate($request, $rules, $messages);
            $user->password = Hash::make($request->password1);
        }

        if (isset($user->email) && isset($user->password)) {
            switch ($method) {
                case 'steam':
                    $user->steamname = "";
                    $user->steamid = "";
                    $user->steam_avatar = "";

                    if ($user->selected_avatar == 'steam')
                    {
                        $user->selected_avatar = 'local';
                    }

                    break;
                default:
                    return Redirect::back()->with('error', 'no valid sso method selected');
                    break;
            }
        }

        if (!$user->save()) {
            return Redirect::back()->withFail("Oops, Something went Wrong while updating the user.");
        }

        if ($mailchanged) {
            Session::flash('alert-success', "Successfully removed steam account, email verification is needed!");
            $user->sendEmailVerificationNotification();
            return redirect('/register/email/verify');
        } else {
            Session::flash('alert-success', "Successfully removed steam account!");
            return redirect('/account');
        }
    }



    public function update(Request $request)
    {
        $rules = [
            'firstname'     => 'filled',
            'surname'       => 'filled',
            'password1'     => 'same:password2',
            'password2'     => 'same:password1',
            'locale'        => ['nullable', new ValidLocale]
        ];
        $messages = [
            'firstname.filled'  => 'Firstname Cannot be blank.',
            'surname.filled'    => 'Surname Cannot be blank.',
            'email.email'       => 'Email must be a valid Email Address.',
            'password1.same'    => 'Passwords must be the same.',
            'password2.same'    => 'Passwords must be the same.',
        ];
        $this->validate($request, $rules, $messages);

        $user = Auth::user();
        if (isset($request->password1) && $request->password1 != null) {
            $rules = [
                'password1'     => 'same:password2|min:8',
                'password2'     => 'same:password1|min:8',
            ];
            $messages = [
                'password1.same'    => 'Passwords must be the same.',
                'password1.min'     => 'Password must be atleast 8 characters long.',
                'password2.same'    => 'Passwords must be the same.',
                'password2.min'     => 'Password must be atleast 8 characters long.',
            ];
            $this->validate($request, $rules, $messages);
            $user->password = Hash::make($request->password1);
        }

        $user->firstname = @$request->firstname;
        $user->surname = @$request->surname;

        if (isset($request->locale)) {
            $user->locale = @$request->locale;
        }

        if (!$user->save()) {
            return Redirect::back()->withFail("Oops, Something went Wrong.");
        }
        return Redirect::back()->with('success', 'Account successfully updated!');
    }

    public function updateMail(Request $request)
    {
        $user = Auth::user();
        $rules = [];
        $messages = [];
        $email_changed = $user->email != @$request->email;

        if (Settings::isAuthSteamRequireEmailEnabled() && $email_changed) {
            $rules['email'] = 'filled|email|unique:users,email';
            $messages['email.filled'] = 'Email Cannot be blank.';
            $messages['email.unique'] = 'Email is already in use.';
        }

        if (Settings::isAuthRequirePhonenumberEnabled()) {
            $rules['phonenumber'] = 'required|filled|phone:AUTO,DE';
            $messages['phonenumber.phone'] = 'The field contains an invalid number.';
        }

        $this->validate($request, $rules, $messages);


        if ($email_changed) {
            $user->email_verified_at = null;
        }

        $user->email = @$request->email;
        $user->phonenumber = @$request->phonenumber;

        if (!$user->save()) {
            return Redirect::back()->withFail("Oops, Something went Wrong while updating the user.");
        }
        if ($email_changed) {
            $user->sendEmailVerificationNotification();
            return redirect('/register/email/verify');
        }
        if ($request->session()->get('eventula_req_url') != "")
        {
            return redirect($request->session()->get('eventula_req_url'));
        }
        return redirect('/');
    }

    public function update_local_avatar(Request $request) {
        $this->validate($request, [
            'avatar' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        if(!$path = Storage::putFile(
            'public/images/avatars', $request->file('avatar')
        ))
        {
            Session::flash('alert-danger', 'Oops,Something went wrong while uploading the File on the custom avatar upload.');
            return Redirect::back();
        }
        $user = Auth::user();
        $user->local_avatar = '/storage/images/avatars/' . basename($path);
        $user->selected_avatar = 'local';
        if (!$user->save()) {
            Session::flash('alert-danger', 'Oops, Something went wrong while updating the user on the custom avatar upload.');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Custom avatar successfully updated!');
        return Redirect::back();
    }

    public function update_selected_avatar(Request $request) {

        $this->validate($request, [
            'selected_avatar' => 'required|in:steam,local',
        ]);

        $user = Auth::user();
        $user->selected_avatar = $request->selected_avatar;
        if (!$user->save()) {
            Session::flash('alert-danger', 'Oops, Something went wrong while updating the user on the selected avatar change.');
            return Redirect::back();
        }

        Session::flash('alert-success', 'Selected avatar successfully updated!');
        return Redirect::back();
    }


}
