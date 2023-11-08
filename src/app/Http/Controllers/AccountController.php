<?php

namespace App\Http\Controllers;

use App\EventParticipant;
use DB;
use Auth;
use Dompdf\Dompdf;
use Settings;
use Session;
use Colors;


use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        $tickets = $user->eventParticipants()->paginate(5, ['*'], 'ti');
        return view("accounts.index")
            ->withUser($user)
            ->withCreditLogs($creditLogs)
            ->withPurchases($purchases)
            ->withEventParticipants($tickets);
    }

    /**
     * Show Email Change Page
     * @return View
     */
    public function showMail()
    {
        $user = Auth::user();

        return view("accounts.email")
            ->withUser($user);
    }

    /**
     * Show Remove single sign on Page
     * @return View
     */
    public function showRemoveSso($method)
    {
        $user = Auth::user();

        return view("accounts.removesso")
            ->withUser($user)
            ->withMethod($method);
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
            return view("accounts.tokenwizzard_start")->withStatus('no_application');
        }


        foreach ($user->tokens as $currtoken) {
            if ($currtoken->name == $application) {
                return view("accounts.tokenwizzard_start")->withStatus('exists')->withApplication($application)->withCallbackurl($callbackurl);
            }
        }

        return view("accounts.tokenwizzard_start")->withStatus("not_exists")->withApplication($application)->withCallbackurl($callbackurl);
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
                    return view("accounts.tokenwizzard_finish")->withStatus('del_failed')->withApplication($request->application);
                }
            }
        }



        $token = $user->createToken($request->application);

        if ($token->plainTextToken == null || $token->plainTextToken == "") {
            return view("accounts.tokenwizzard_finish")->withStatus('creation_failed')->withApplication($request->application);
        }

        $newcallbackurl = $request->callbackurl . "://" . $token->plainTextToken;

        return view("accounts.tokenwizzard_finish")->withStatus('success')->withNewtoken($token->plainTextToken)->withApplication($request->application)->withCallbackurl($newcallbackurl);
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
                return Redirect::back()->withError('no valid sso method selected');
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
                    $user->avatar = "";
                    break;
                default:
                    return Redirect::back()->withError('no valid sso method selected');
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

        if (!$user->save()) {
            return Redirect::back()->withFail("Oops, Something went Wrong.");
        }
        return Redirect::back()->withSuccess('Account successfully updated!');
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

    public function pdfTicket(string $ticketId): Response {
        $user = Auth::user();
        $participant = EventParticipant::where('id', $ticketId)->first();
        if ($user->id != $participant->user_id) {
            return response()->view('errors.403', [], Response::HTTP_FORBIDDEN);
        }

        // TODO: Probably don't use str_replace
        $qrfile = base64_encode(\Storage::read(str_replace('storage', 'public', $participant->qrcode)));
        $qrimage = "data:image/png;base64,{$qrfile}";

        $pdfView = view('ticket.pdf')
            ->with('participant', $participant)
            ->with('qrimage', $qrimage)
            ->render();

        $pdf = new Dompdf();
        $pdf->setPaper('A4', 'portrait');
        $pdf->loadHtml($pdfView);
        $pdf->render();

        $res = \Response::make($pdf->output());
        $res->header('Content-Type', 'application/pdf');
        return $res;
    }
}
