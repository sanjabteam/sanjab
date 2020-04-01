<?php

namespace Sanjab\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sanjab\Helpers\NotificationItem;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends SanjabController
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = '/'.ltrim(config('sanjab.route'), '/');
        $this->middleware(\Sanjab\Middleware\SanjabGuestMiddleware::class)->except('logout');
    }

    public function loginPage(Request $request)
    {
        return view('sanjab::login');
    }

    public function username()
    {
        return config('sanjab.login.username');
    }

    public function validateLogin(Request $request)
    {
        $request->validate(
            [
                $this->username() => 'required',
                'password'              => 'required',
                'g-recaptcha-response'  => config('sanjab.login.recaptcha') ? 'sanjab_recaptcha' : '',
            ],
            [],
            [
                $this->username()       => config('sanjab.login.title'),
                'password'              => trans('sanjab::sanjab.password'),
            ]
        );
    }

    public static function notifications(): array
    {
        return [
            NotificationItem::create('person')
                ->title(Auth::user()->name)
                ->icon('person')
                ->addItem(Auth::user()->name, '#')
                ->addDivider()
                ->addItem(trans('sanjab::sanjab.logout'), route('sanjab.auth.logout')),
        ];
    }

    public static function routes(): void
    {
        Route::get('auth/logout', static::class.'@logout')->name('auth.logout');
    }
}
