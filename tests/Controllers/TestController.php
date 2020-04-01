<?php

namespace Sanjab\Tests\Controllers;

use Sanjab\Cards\StatsCard;
use Sanjab\Helpers\MenuItem;
use Sanjab\Helpers\SearchResult;
use Sanjab\Helpers\MaterialIcons;
use Sanjab\Helpers\PermissionItem;
use Sanjab\Helpers\NotificationItem;
use Illuminate\Support\Facades\Route;
use Sanjab\Controllers\SanjabController;

/**
 * Test a very base controller.
 */
class TestController extends SanjabController
{
    public function testRoute()
    {
        return 'Hello';
    }

    public static function routes(): void
    {
        Route::get('test-route', static::class.'@testRoute')->name('test-route');
    }

    public static function menus(): array
    {
        return [
            MenuItem::create(route('sanjab.test-route'))
                ->title('Hello World')
                ->badge(function () {
                    return '10';
                })
                ->addChild(MenuItem::create(route('sanjab.test-route'))->title('Child menu item')),
        ];
    }

    public static function notifications(): array
    {
        return [
            NotificationItem::create(MaterialIcons::CHAT_BUBBLE)
                ->addItem('Hello', route('sanjab.test-route')),
        ];
    }

    public static function permissions(): array
    {
        return [
            PermissionItem::create('Hello Group')
                ->addPermission('Access to test controller', 'access_to_test_controller'),
        ];
    }

    public static function dashboardCards(): array
    {
        return [
            StatsCard::create('Test Card')
                    ->value('Hello'),
        ];
    }

    public static function globalSearch(string $search): array
    {
        if ($search == 'test controller global search') {
            return [
                SearchResult::create('Hello', route('sanjab.test-route')),
            ];
        }

        return [];
    }
}
