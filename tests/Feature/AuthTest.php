<?php

namespace Sajab\Tests\Feature;

use Sanjab\Tests\TestCase;
use Sanjab\Tests\Models\User;

class AuthTest extends TestCase
{
    public function testRedirectGuestToLogin()
    {
        $response = $this->get(route('sanjab.dashboards.dashboard'));

        $response->assertStatus(302)
            ->assertRedirect(route('sanjab.auth.login'));
    }

    public function testAbortIfCannotAccessSanjab()
    {
        $this->actingAs(User::where('email', 'normal@test.com')->firstOrFail())
                ->get(route('sanjab.dashboards.dashboard'))
                ->assertStatus(403);
    }

    public function testRedirectIfUserLoggedInBefore()
    {
        $this->actingAs(User::where('email', 'admin@test.com')->firstOrFail())
                ->get(route('sanjab.auth.login'))
                ->assertRedirect(route('sanjab.dashboards.dashboard'));
    }

    public function testCanSeeDashboard()
    {
        $this->actingAs(User::where('email', 'admin@test.com')->firstOrFail())
                ->get(route('sanjab.dashboards.dashboard'))
                ->assertSeeText(config('app.name'));
    }
}
