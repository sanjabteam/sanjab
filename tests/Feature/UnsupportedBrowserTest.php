<?php

namespace Sajab\Tests\Feature;

use Sanjab\Tests\TestCase;
use Sanjab\Tests\App\Models\User;

class UnsupportedBrowserTest extends TestCase
{
    public function testRedirectUnsupportedBrowser()
    {
        $response = $this->actingAs(User::where('email', 'admin@test.com')->firstOrFail())
                ->get(route('sanjab.dashboards.dashboard'), [
                    'HTTP_USER_AGENT' => 'MSIE',
                ]);

        $response->assertRedirect(route('sanjab.unsupported-browser'));
    }
}
