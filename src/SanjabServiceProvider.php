<?php

namespace Sanjab;

use Bouncer;
use ReCaptcha\ReCaptcha;
use TusPhp\Cache\FileStore;
use TusPhp\Events\TusEvent;
use TusPhp\Tus\Server as TusServer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use ReCaptcha\RequestMethod\CurlPost;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class SanjabServiceProvider extends ServiceProvider
{
    protected $commands = [
        \Sanjab\Commands\MakeAdmin::class,
        \Sanjab\Commands\Clear::class,
        \Sanjab\Commands\Install::class,
        \Sanjab\Commands\MakeDashboard::class,
        \Sanjab\Commands\MakeCrud::class,
        \Sanjab\Commands\MakeSetting::class,
        \Sanjab\Commands\SettingsExport::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'sanjab');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sanjab');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutes();
        $this->loadViewGlobalVariables();
        $this->validationRules();

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
        $this->commands($this->commands);

        Gate::policy(\Silber\Bouncer\Database\Role::class, \Sanjab\Policies\RolePolicy::class);
        Gate::policy(config('auth.providers.users.model'), \Sanjab\Policies\UserPolicy::class);
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
        $this->registerTus();
        $this->registerPluginsProvider();
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
            $view->with('sanjabImage', app('sanjab')->image());
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
            if (config('app.debug') && config('sanjab.recaptcha.ignore_on_debug')) {
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

    /**
     * Register php tus.
     *
     * @return void
     */
    public function registerTus()
    {
        $this->app->singleton('sanjab-tus-server', function ($app) {
            if (! Storage::disk('local')->exists('temp/'.Session::getId())) {
                Storage::disk('local')->makeDirectory('temp/'.Session::getId());
            }

            $server = new TusServer(
                new FileStore(Storage::disk('local')->path('temp/'), Session::getId().'_tus_php.server.cache')
            );

            $server->event()->addListener('tus-server.upload.complete', function (TusEvent $event) {
                $uploadedFiles = Session::get('sanjab_uppy_files');
                $uploadedFiles[$event->getFile()->getKey()] = $event->getFile()->details();
                Session::put('sanjab_uppy_files', $uploadedFiles);
            });

            $server
                ->setApiPath('/admin/helpers/uppy/upload')
                ->setUploadDir(Storage::disk('local')->path('temp/'.Session::getId()));

            return $server;
        });
    }

    /**
     * Register sanjab plugins provider.
     *
     * @return void
     */
    public function registerPluginsProvider()
    {
        if (is_array(config('sanjab.plugins.providers'))) {
            foreach (config('sanjab.plugins.providers') as $provider) {
                $this->app->register($provider);
            }
        }
    }
}
