<?php

namespace Sajab\Tests\Feature;

use Sanjab\Tests\TestCase;
use Sanjab\Tests\App\Models\User;
use Silber\Bouncer\BouncerFacade as Bouncer;

class ArtisanTest extends TestCase
{
    public function testMakeAdmin()
    {
        $normalUser = User::where('email', 'normal@test.com')->first();
        $this->assertFalse($normalUser->isA('super_admin'));

        $this->artisan('sanjab:make:admin')
            ->expectsQuestion(config('sanjab.login.username'), 'nouser@test.com')
            ->expectsOutput('User does not exists!')
            ->expectsQuestion(config('sanjab.login.username'), 'normal@test.com');

        Bouncer::refreshFor($normalUser);
        $this->assertTrue($normalUser->isA('super_admin'));
        $normalUser->retract('super_admin');
    }
}
