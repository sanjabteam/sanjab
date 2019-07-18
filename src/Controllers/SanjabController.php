<?php

namespace Sanjab\Controllers;

use Sanjab\Helpers\MenuItem;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Sanjab\Helpers\PermissionItem;
use Sanjab\Helpers\NotificationItem;

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
     * @return MenuItem[]
     */
    public static function menus(): array
    {
        return [];
    }

    /**
     * Load sanjab notification items.
     *
     * @return NotificationItem[]
     */
    public static function notifications(): array
    {
        return [];
    }

    /**
     * Load sanjab permissions for this controller.
     *
     * @return PermissionItem[]
     */
    public static function permissions(): array
    {
        return [];
    }
}
