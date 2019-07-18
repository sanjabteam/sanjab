<?php

namespace Sanjab\Middleware;

use Closure;

class SanjabMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->user()) {
            return redirect()->route('sanjab.auth.login');
        }
        if ($request->user()->cannot('access_sanjab')) {
            return abort(403);
        }

        $response = $next($request);
        $request->session()->put('sanjab_hide_lock_screen', time());
        return $response;
    }
}
