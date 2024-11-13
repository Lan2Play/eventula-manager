<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;

use Illuminate\Support\Facades\Redirect;

class Banned
{

    public function handle($request, Closure $next)
    {

        if (Auth::check() && !Auth::user()->banned) {
            return $next($request);
        }

 		Session::flash('alert-danger', 'You have been banned!');
        return Redirect::back()->with('error', 'You have been banned.');
    }
}
