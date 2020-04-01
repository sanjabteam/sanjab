<?php

namespace Sanjab\Tests\Browser;

use Laravel\Dusk\Browser;
use Sanjab\Tests\TestCase;
use Sanjab\Tests\Models\User;

class DashboardTest extends TestCase
{
    public function testRedirectUnathorizedUser()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('sanjab.dashboards.dashboard'))
                    ->waitForRoute('sanjab.auth.login')
                    ->assertRouteIs('sanjab.auth.login');
        });
    }
    public function testCanSeeDashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::where('email', 'admin@test.com')->firstOrFail())
                    ->visit(route('sanjab.dashboards.dashboard'))
                    ->assertRouteIs('sanjab.dashboards.dashboard')
                    ->assertSee(trans('sanjab::sanjab.welcome_back'));
        });
    }

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('sanjab.auth.login'))
                    ->assertSee(trans('sanjab::sanjab.login'))
                    ->type('email', 'admin@test.com')
                    ->type('password', '111111')
                    ->click('button[type="submit"]')
                    ->waitFor('.alert-danger')
                    ->type('password', '123456')
                    ->click('button[type="submit"]')
                    ->waitForRoute('sanjab.dashboards.dashboard');
        });
    }
}
