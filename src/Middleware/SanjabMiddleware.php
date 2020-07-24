<?php

namespace Sanjab\Middleware;

use Closure;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\Route;

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
        // Check unsupported browsers
        $browserUnsupported = (Agent::is('IE') || Agent::is('Edge') || Agent::is('Opera Mini') || Agent::is('Netscape'));
        if (! Route::is('sanjab.unsupported-browser') && $browserUnsupported) {
            return redirect()->route('sanjab.unsupported-browser');
        }

        // Unauthorized users
        if (! $request->user()) {
            return redirect()->route('sanjab.auth.login');
        }

        // Non admin users
        if ($request->user()->cannot('access_sanjab')) {
            abort(403);
        }

        $response = $next($request);
        $request->session()->put('sanjab_hide_screen_saver', time());

        return $response;
    }
}
