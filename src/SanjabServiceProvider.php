<?php

namespace Sanjab;

use Bouncer;
use ReCaptcha\ReCaptcha;
use Illuminate\Support\Facades\View;
use ReCaptcha\RequestMethod\CurlPost;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class SanjabServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'sanjab');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sanjab');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutes();
        $this->loadViewGlobalVariables();
        $this->validationRules();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('sanjab.php'),
            ], 'config');

            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/sanjab'),
            ], 'views');

            // Publishing assets.
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/sanjab'),
            ], 'assets');

            // Publishing the translation files.
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/sanjab'),
            ], 'lang');

            // Registering package commands.
            $this->commands([
                \Sanjab\Commands\MakeAdmin::class
            ]);
        }

        Gate::policy(\Silber\Bouncer\Database\Role::class, \Sanjab\Policies\RolePolicy::class);
        Bouncer::runAfterPolicies();
        Bouncer::tables([
            'abilities'      => 'bouncer_abilities',
            'assigned_roles' => 'bouncer_assigned_roles',
            'permissions'    => 'bouncer_permissions',
            'roles'          => 'bouncer_roles',
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'sanjab');

        $this->app->singleton('sanjab', function () {
            return new Sanjab;
        });
    }

    /**
     * Load sanjab controller routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        if (! $this->app->routesAreCached()) {
            Route::middleware(['web'])
                    ->name('sanjab.')
                    ->prefix(config('sanjab.route'))
                    ->namespace('Sanjab\\Controllers')
                    ->group(sanjab_path('routes/routes.php'));

            Route::middleware(['web', \Sanjab\Middleware\SanjabMiddleware::class])
                    ->name('sanjab.')
                    ->prefix(config('sanjab.route'))
                    ->group(function () {
                        foreach (app('sanjab')->controllers() as $controller) {
                            $controller::routes();
                        }
                    });
        }
    }

    /**
     * Load views variables.
     *
     * @return void
     */
    protected function loadViewGlobalVariables()
    {
        View::composer(['admin.*', 'sanjab.*', 'sanjab::*'], function (\Illuminate\View\View $view) {
            $view->with('sanjabMenuItems', app('sanjab')->menuItems());
            $view->with('sanjabNotificationItems', app('sanjab')->notificationItems());
        });
    }

    /**
     * Load custom validation rules.
     *
     * @return void
     */
    protected function validationRules()
    {
        Validator::extendImplicit('sanjab_recaptcha', function ($attribute, $value, $parameters, $validator) {
            if (config("app.debug") && config('sanjab.recaptcha.ignore_on_debug')) {
                return true;
            }
            if (empty($value)) {
                return false;
            }
            $recaptcha = new ReCaptcha(config('sanjab.recaptcha.secret_key'), new CurlPost());
            $response = $recaptcha->verify($value, request()->ip());
            if ($response->isSuccess()) {
                return true;
            }
            return false;
        });

        Validator::replacer('sanjab_recaptcha', function ($message, $attribute, $rule, $parameters) {
            return trans('sanjab::sanjab.please_click_on_im_not_robot');
        });
    }
}
