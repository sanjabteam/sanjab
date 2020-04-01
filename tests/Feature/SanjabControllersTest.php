<?php

namespace Sajab\Tests\Feature;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Sanjab\Cards\Card;
use Sanjab\Helpers\MenuItem;
use Sanjab\Helpers\NotificationItem;
use Sanjab\Helpers\PermissionItem;
use Sanjab\Sanjab;
use Sanjab\Tests\Models\User;
use Sanjab\Tests\TestCase;

/**
 * Test everything working on \Sanjab\Tests\Controllers\TestController::class,
 */
class SanjabControllersTest extends TestCase
{
    public function testUndefinedControllerLog()
    {
        $controllerClasses = config('sanjab.controllers');
        config(['sanjab.controllers' => array_merge($controllerClasses, ['UndefinedClass'])]);
        if (File::exists(storage_path('logs/laravel.log'))) {
            File::delete(storage_path('logs/laravel.log'));
        }
        $controllers = Sanjab::controllers();
        $this->assertFalse(array_search('UndefinedClass', $controllers, true));
        $this->assertTrue(File::exists(storage_path('logs/laravel.log')));
        $this->assertTrue(str_contains(File::get(storage_path('logs/laravel.log')), '\'UndefinedClass\' is not a valid sanjab controller.'));

        config(['sanjab.controllers' => $controllerClasses]);
    }

    public function testMenuItems()
    {
        $menuItems = Sanjab::menuItems();
        $this->assertTrue(count($menuItems) == 0);

        Auth::loginUsingId(User::where('email', 'admin@test.com')->firstOrFail()->id);
        $menuItems = Sanjab::menuItems();
        $this->assertFalse(count($menuItems) == 0);

        $this->assertTrue(count(array_filter($menuItems, function (MenuItem $menuItem) {
            return $menuItem->property('url') == route('sanjab.test-route') && $menuItem->property('title') == 'Hello World';
        })) == 1);
        Auth::logout();
    }

    public function testPermissionItems()
    {
        $permissionItems = Sanjab::permissionItems();
        $this->assertFalse(count($permissionItems) == 0);

        $this->assertTrue(count(array_filter($permissionItems, function (PermissionItem $permissionItem) {
            return $permissionItem->property('groupName') == 'Hello Group' && count($permissionItem->permissions()) > 0;
        })) == 1);
    }

    public function testNotificationItems()
    {
        $notificationItems = Sanjab::notificationItems();
        $this->assertTrue(count($notificationItems) == 0);

        Auth::loginUsingId(User::where('email', 'admin@test.com')->firstOrFail()->id);
        $notificationItems = Sanjab::notificationItems();
        $this->assertFalse(count($notificationItems) == 0);

        $this->assertTrue(count(array_filter($notificationItems, function (NotificationItem $notificationItems) {
            return $notificationItems->property('icon') == 'chat_bubble' && count($notificationItems->getItems()) > 0;
        })) == 1);
        Auth::logout();
    }

    public function testDashboardCarts()
    {
        Auth::loginUsingId(User::where('email', 'admin@test.com')->firstOrFail()->id);
        $dashboardCards = Sanjab::dashboardCards();
        $this->assertFalse(count($dashboardCards) == 0);

        $this->assertTrue(count(array_filter($dashboardCards, function (Card $card) {
            return $card->property('title') == 'Test Card' && $card->property('value') == 'Hello';
        })) == 1);
        Auth::logout();
    }

    public function testGlobalSearch()
    {
        Auth::loginUsingId(User::where('email', 'admin@test.com')->firstOrFail()->id);
        $searchResult = Sanjab::search('some thing to not find any where');
        $this->assertTrue(count($searchResult) == 0);
        $searchResult = Sanjab::search('test controller global search');
        $this->assertTrue(count($searchResult) == 1);
        Auth::logout();
    }
}
