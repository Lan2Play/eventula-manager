<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;


class Legacywarning
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        if ((Auth::check() && Auth::user()->getAdmin())) {
            Auth::getSession()->flash('alert-warning', __('legacywarning.infotext'));
        }


        return $next($request);
    }
}
