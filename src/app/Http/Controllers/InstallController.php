<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Settings;
use Colors;
use Session;
use Artisan;

use App\ApiKey;
use App\Event;
use App\User;
use App\SliderImage;
use App\NewsArticle;
use App\EventTimetable;
use App\EventTimetableData;
use App\EventParticipant;
use App\EventTournamentTeam;
use App\EventTournamentParticipant;

use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;


class InstallController extends Controller
{
    /**
     * Show Installation Page
     * @param  Event  $event
     * @return View
     */
    public function installation()
    {
        return view("install");
    }

    /**
     * Install App
     * @param  Request $request
     * @return Redirect
     */
    public function install(Request $request)
    {
        $rules = [
            'email'         	=> 'required|filled|email|unique:users,email',
            'password1'     	=> 'required|same:password2|min:8',
            'username'      	=> 'required|unique:users,username',
            'firstname'     	=> 'required|string',
            'surname'       	=> 'required|string',
            'org_name'			=> 'filled',
            'org_tagline'		=> 'filled',
            'paypal_username'	=> 'required_without:stripe_secret,stripe_public',
			'paypal_password'	=> 'required_without:stripe_secret,stripe_public',
			'paypal_signature'	=> 'required_without:stripe_secret,stripe_public',
			'stripe_public'		=> 'required_without:paypal_signature,paypal_password,paypal_username',
			'stripe_secret'		=> 'required_without:paypal_signature,paypal_password,paypal_username',
        ];
        $messages = [
            'username.unique'       				=> 'Username must be unique',
            'username.required'     				=> 'Username is required',
            'email.filled'          				=> 'Email Cannot be blank.',
            'email.required'        				=> 'Email is required.',
            'email.email'           				=> 'Email must be a valid Email Address.',
            'email.unique'          				=> 'Email must be unique.',
            'password1.same'        				=> 'Passwords must be the same.',
            'password1.required'    				=> 'Password is required.',
            'password1.min'         				=> 'Password must be atleast 8 characters long.',
            'org_name.filled'						=> 'Org Name cannot be empty.',
            'org_tagline.filled'					=> 'Org Tagline cannot be empty.',
            'paypal_username.required_without'		=> 'Paypal Username is required if no other details are entered.',
            'paypal_password.required_without'		=> 'Paypal Password is required if no other details are entered.',
            'paypal_signature.required_without'		=> 'Paypal Signature is required if no other details are entered.',
            'stripe_public.required_without'		=> 'Stripe Public Key is required if no other details are entered.',
            'stripe_secret.required_without'		=> 'Stripe Secret Key is required if no other details are entered.',
        ];
        $this->validate($request, $rules, $messages);

        // Create User
        $user 						= New User;
     	$user->email          		= $request->email;
     	$user->admin 				= true;
        $user->password       		= Hash::make($request->password1);
        $user->firstname        	= $request->firstname;
        $user->surname          	= $request->surname;
        $user->username         	= $request->username;
        $user->username_nice    	= strtolower(str_replace(' ', '-', $request->username));
        $user->email_verified_at	= new \DateTime('NOW');
        $user->save();
        Auth::login($user, true);

        // Set Org Details
        Settings::SetOrgName($request->org_name);
        Settings::SetOrgTagline($request->org_tagline);

        // Set Payment Gateways
        if ($request->paypal_username != '' && $request->paypal_password != '' && $request->paypal_signature != '') {
	        Apikey::setPaypalUsername($request->paypal_username);
	        Apikey::setPaypalPassword($request->paypal_password);
	        Apikey::setPaypalSignature($request->paypal_signature);
	        Settings::enablePaymentGateway('paypal');
        }
        if ($request->stripe_public != '' && $request->stripe_secret != '') {
	        Apikey::setStripePublicKey($request->stripe_public);
	        Apikey::setStripeSecretKey($request->stripe_secret);
	        Settings::enablePaymentGateway('stripe');
        }
		Settings::enablePaymentGateway('free');
		Settings::enablePaymentGateway('onsite');

        // Clear Cache
        Artisan::call('config:clear');

        // Set Installed
        Settings::setInstalled();

        Session::flash('alert-info', 'Installation Complete. Have a look around the Settings to make sure everything is Hunky Dory and you are good to go!');
        return Redirect::to('/admin/settings');
    }
}