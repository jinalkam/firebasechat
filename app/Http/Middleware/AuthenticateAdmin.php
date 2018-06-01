<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateAdmin
{
    /**
     * Check if admin is authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
         $auth = Auth::guard('admin');
        if (!$auth->check()) {


            return redirect('/admin');
        }


        return $next($request);
    }
}
