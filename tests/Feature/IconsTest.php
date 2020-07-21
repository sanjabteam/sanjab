<?php

namespace Sanjab\Tests\Feature;

use Sanjab\Tests\TestCase;
use Sanjab\Tests\App\Models\User;

class IconsTest extends TestCase
{
    public function testIconsPage()
    {
        $response = $this->actingAs(User::where('email', 'admin@test.com')->firstOrFail())
                ->get(route('sanjab.icons'));

        $response->assertStatus(200);
    }
}
