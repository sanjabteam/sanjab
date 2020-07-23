<?php

namespace Sanjab\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sanjab\Plugins\Notification\NotificationController;
use Sanjab\Tests\TestCase;
use Sanjab\Tests\App\Models\User;
use Sanjab\Tests\App\Notifications\TestNotification;

class NotificationsTest extends TestCase
{
    public function testNotificationsPage()
    {
        $response = $this->actingAs(User::where('email', 'admin@test.com')->firstOrFail())
                            ->get(route('sanjab.modules.notifications.index'));

        $response->assertStatus(200);
    }

    public function testNotificationsAreOnlyForCurrentUser()
    {
        $response = $this->actingAs(User::where('email', 'admin@test.com')->firstOrFail())
                ->getJson(route('sanjab.modules.notifications.index'));

        $response->assertStatus(200)
                ->assertJsonCount(1, 'items.data')
                ->assertJson([
                    'items' => [
                        'data' => [
                            [
                                'text' => 'hello admin user',
                            ],
                        ],
                    ],
                ]);
    }

    public function testOpenUrl()
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $admin->notify(new TestNotification('Url Test', 'http://sanjabteam.github.io/'));
        Auth::loginUsingId($admin->id);

        $notificatios = NotificationController::notifications();
        $this->assertCount(5, $notificatios[0]->getItems());
        foreach ($notificatios[0]->getItems() as $notificatioItem) {
            if ($notificatioItem['title'] == 'Url Test') {
                $response = $this->get($notificatioItem['link']);

                $response->assertRedirect('http://sanjabteam.github.io/');
            }
        }

        $this->assertEquals(1, $admin->unreadNotifications()->count());

        Auth::logout();
        DB::table('notifications')->whereNotNull('read_at')->delete();
    }

    public function testMarkAsRead()
    {
        $admin = User::where('email', 'admin@test.com')->firstOrFail();
        $response = $this->actingAs($admin)
                            ->getJson(route('sanjab.notifications.mark-as-read'));

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        $this->assertEquals(0, $admin->unreadNotifications()->count());
        DB::table('notifications')->update(['read_at' => null]);
    }
}
