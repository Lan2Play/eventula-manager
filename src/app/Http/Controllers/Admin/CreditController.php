<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Settings;
use Colors;

use App\User;
use App\CreditLog;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CreditController extends Controller
{
    /**
     * Show Credit System Index Page
     * @return View
     */
    public function index()
    {
        $users = User::get();
        $selectallusers = array();
        foreach ($users as $user) {
            $selectallusers[$user->id] = $user->username;
        }
        return view('admin.credit.index')
            ->with('isCreditEnabled', Settings::isCreditEnabled())
            ->with('creditLogs', CreditLog::paginate(10, ['*'], 'cl'))
            ->with('users', $selectallusers);
    }

    /**
     * Manually Edit Credit to User
     * @return View
     */
    public function edit(Request $request)
    {
	 	$rules = [
            'user_id'   => 'required|exists:users,id',
            'amount'    => 'required|integer',
        ];
      	$messages = [
            'user_id.required'	=> 'User ID is Required',
            'user_id.exists'	=> 'User ID must be a valid user.',
            'amount.required' 	=> 'An Amount is Required',
            'amount.integer'  	=> 'Amount must be a number',
        ];
        $this->validate($request, $rules, $messages);

        $user = User::where('id', $request->user_id)->first();
        if (!$user->checkCredit($request->amount)) {
			Session::flash('alert-danger', 'Not enough Credit.');
        	return Redirect::back();
        }
        $action = 'add';
        if ($request->amount < 0) {
        	$action = 'subtract';
        }
        if (!$user->editCredit($request->amount, true)) {
        	Session::flash('alert-danger', 'Could not ' . $action . ' credit. Please try again.');
        	return Redirect::back();
        }
        $action = 'added';
        if ($request->amount < 0) {
        	$action = 'subtracted';
        }
        Session::flash('alert-success', 'Successfully ' . $action . ' ' . $request->amount . ' Credits.');
    	return Redirect::back();
    }
}
