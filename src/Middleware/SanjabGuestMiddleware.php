<?php

namespace Sanjab\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SanjabGuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return redirect('/'.ltrim(config('sanjab.route'), '/'));
        }
        $request->session()->forget('sanjab_hide_screen_saver');

        return $next($request);
    }
}
