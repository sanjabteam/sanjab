<?php

namespace Sanjab\Controllers;

use Sanjab\Helpers\MenuItem;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Sanjab\Helpers\PermissionItem;
use Sanjab\Helpers\NotificationItem;
use Sanjab\Cards\Card;
use Sanjab\Helpers\SearchResult;

abstract class SanjabController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Submit controller routes in this function.
     *
     * @return void
     */
    public static function routes(): void
    {
        //
    }

    /**
     * Load sanjab panel menu items for this controller.
     *
     * @return array|MenuItem[]
     */
    public static function menus(): array
    {
        return [];
    }

    /**
     * Load sanjab notification items.
     *
     * @return array|NotificationItem[]
     */
    public static function notifications(): array
    {
        return [];
    }

    /**
     * Load sanjab permissions for this controller.
     *
     * @return array|PermissionItem[]
     */
    public static function permissions(): array
    {
        return [];
    }

    /**
     * Load sanjab dashboard cards for this controller.
     *
     * @return array|Card[]
     */
    public static function dashboardCards(): array
    {
        return [];
    }

    /**
     * Global search in this controller.
     *
     * @param string $search  text to search.
     * @return array|SearchResult[]
     */
    public static function globalSearch(string $search): array
    {
        return [];
    }
}
