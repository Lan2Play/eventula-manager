<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Settings;
use Colors;
use Session;

use App\User;
use App\Event;
use App\GalleryAlbum;
use App\GalleryAlbumImage;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Show Users Index Page
     * @return View
     */
    public function index(Request $request)
    {
        if ($request->searchquery != "") {
            return view('admin.users.index')
                ->with('user', Auth::user())
                ->withUsers(
                    User::where('username', 'LIKE', '%' . $request->searchquery . '%')
                        ->orWhere('firstname', 'LIKE', '%' . $request->searchquery . '%')
                        ->orWhere('surname', 'LIKE', '%' . $request->searchquery . '%')
                        ->orWhere('username_nice', 'LIKE', '%' . $request->searchquery . '%')
                        ->orWhere('steamname', 'LIKE', '%' . $request->searchquery . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->searchquery . '%')
                        ->orWhere('phonenumber', 'LIKE', '%' . $request->searchquery . '%')
                        ->paginate(20)->appends(['searchquery' =>  $request->searchquery])

                );
        }

        return view('admin.users.index')
            ->with('user', Auth::user())
            ->with('users', User::paginate(20)->appends(['searchquery' =>  $request->searchquery]));
    }

    /**
     * Show User Page
     * @return View
     */
    public function show(User $user)
    {
        $creditLogs = false;
        if (Settings::isCreditEnabled()) {
            $creditLogs = $user->creditLogs()->paginate(5, ['*'], 'cl');
        }
        return view('admin.users.show')
            ->with('userShow', $user)
            ->with('creditLogs', $creditLogs)
            ->with('purchases', $user->purchases()->paginate(10, ['*'], 'pu'));
    }

    /**
     * Grant User Admin
     * @param  User  $user
     * @return View
     */
    public function grantAdmin(User $user)
    {
        if (!Auth::user()->admin) {
            Session::flash('alert-danger', 'You do not have permissions to do this!');
            return Redirect::back();
        }
        $user->admin = true;
        if (!$user->save()) {
            Session::flash('alert-danger', 'Cannot grant admin!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated user!');
        return Redirect::back();
    }

    /**
     * Remove User Admin
     * @param  User  $user
     * @return View
     */
    public function removeAdmin(User $user)
    {
        if (!Auth::user()->admin) {
            Session::flash('alert-danger', 'You do not have permissions to do this!');
            return Redirect::back();
        }
        $user->admin = false;
        if (!$user->save()) {
            Session::flash('alert-danger', 'Cannot remove admin!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated user!');
        return Redirect::back();
    }

    /**
     * Set User Email Verification
     * @param  User  $user
     * @return View
     */
    public function setemailverification(User $user)
    {
        $user->email_verified_at = now();
        if (!$user->save()) {
            Session::flash('alert-danger', 'Cannot set email verification!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated user!');
        return Redirect::back();
    }

    /**
     * Remove User Email Verification
     * @param  User  $user
     * @return View
     */
    public function removemailverification(User $user)
    {
        $user->email_verified_at = null;
        if (!$user->save()) {
            Session::flash('alert-danger', 'Cannot remove email verification!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully updated user!');
        return Redirect::back();
    }


    /**
     * Unauthorize thirdparty
     * @param  User  $user
     * @param  String  $method
     * @return View
     */
    public function unauthorizeThirdparty(User $user, String $method)
    {
        if (!Auth::user()->admin) {
            Session::flash('alert-danger', 'You do not have permissions to do this!');
            return Redirect::back();
        }

        switch ($method) {
            case 'steam':
                $user->steamname = null;
                $user->steamid = null;
                $user->steam_avatar = null;
                if ($user->selected_avatar == 'steam') {
                    $user->selected_avatar = 'local';
                }
                break;
            default:
                Session::flash('alert-danger', 'Cannot remove thirdparty authentication, no method selected!!');
                return Redirect::back();
                break;
        }

        if (!$user->save()) {
            Session::flash('alert-danger', 'Cannot remove thirdparty authentication!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully removed thirdparty authentication!');
        return Redirect::back();
    }


    /**
     * Remove User
     * @param  User  $user
     * @return View
     */
    public function remove(User $user)
    {
        if (!Auth::user()->admin) {
            Session::flash('alert-danger', 'You do not have permissions to do this!');
            return Redirect::back();
        }
        if (!$user->delete()) {
            Session::flash('alert-danger', 'Cannot remove user!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully removed user!');
        return Redirect::to('/admin/users/');
    }



    /**
     * Ban User
     * @param  User  $user
     * @return View
     */
    public function ban(User $user)
    {
        if (!Auth::user()->admin) {
            Session::flash('alert-danger', 'You do not have permissions to do this!');
            return Redirect::back();
        }
        $user->banned = true;
        if (!$user->save()) {
            Session::flash('alert-danger', 'Cannot ban user!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully banned user!');
        return Redirect::back();
    }

    /**
     * Unban User
     * @param  User  $user
     * @return View
     */
    public function unban(User $user)
    {
        if (!Auth::user()->admin) {
            Session::flash('alert-danger', 'You do not have permissions to do this!');
            return Redirect::back();
        }
        $user->banned = false;
        if (!$user->save()) {
            Session::flash('alert-danger', 'Cannot unban user!');
            return Redirect::back();
        }
        Session::flash('alert-success', 'Successfully unbanned user!');
        return Redirect::back();
    }
}
