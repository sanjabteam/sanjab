<?php

namespace Sanjab\Tests;

use Sanjab\Tests\Models\User;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Illuminate\Support\Facades\File;

class DuskTestCase extends \Orchestra\Testbench\Dusk\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }
        \Orchestra\Testbench\Dusk\Options::withoutUI();

        $this->artisan('package:discover');
        $this->artisan('sanjab:install --force');
        $this->artisan('migrate:fresh');
        $this->loadLaravelMigrations('sqlite');

        // Normal user
        User::create(['name' => 'Sanjab', 'email' => 'normal@test.com', 'password' => bcrypt('123456')]);

        // Super admin user
        User::create(['name' => 'Sanjab', 'email' => 'admin@test.com', 'password' => bcrypt('123456')]);
        $this->artisan('sanjab:make:admin --user=admin@test.com');
    }

    protected function getPackageProviders($app)
    {
        return [
            \Sanjab\SanjabServiceProvider::class,
            \Silber\Bouncer\BouncerServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Sanjab' => \Sanjab\SanjabFacade::class,
            'Bouncer' => \Silber\Bouncer\BouncerFacade::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        if (! file_exists(database_path('database.sqlite'))) {
            file_put_contents(database_path('database.sqlite'), '');
        }
        $app['config']->set('auth.providers.users.model', User::class);

        file_put_contents(
            realpath(__DIR__.'/Controllers').'/DashboardController.php',
            str_replace(
                'App\Http\Controllers\Admin',
                'Sanjab\Tests\Controllers',
                file_get_contents(realpath(app_path('Http/Controllers/Admin/DashboardController.php')))
            )
        );
        file_put_contents(
            realpath(__DIR__.'/Controllers').'/UserController.php',
            str_replace(
                ['App\Http\Controllers\Admin\Crud', 'App\User'],
                ['Sanjab\Tests\Controllers', User::class],
                file_get_contents(realpath(app_path('Http/Controllers/Admin/Crud/UserController.php')))
            )
        );

        $app['config']->set('sanjab.controllers', [
            \Sanjab\Tests\Controllers\DashboardController::class,
            \Sanjab\Tests\Controllers\UserController::class,
        ]);
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=1920,1080',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }
}
