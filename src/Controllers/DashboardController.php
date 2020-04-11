<?php

namespace Sanjab\Controllers;

use Sanjab\Helpers\MenuItem;
use Sanjab\Traits\CardHandler;
use Sanjab\Helpers\PermissionItem;
use Illuminate\Support\Facades\Route;
use Sanjab\Helpers\DashboardProperties;

/**
 * Base controller for dashboard classes.
 */
abstract class DashboardController extends SanjabController
{
    use CardHandler;

    /**
     * Show dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $this->initDashboard();
        $cardsData = $this->getCardsData();

        return view('sanjab::dashboard', [
            'properties' => $this->properties(),
            'cards'      => $this->cards,
            'cardsData'  => $cardsData,
        ]);
    }

    /**
     * Get CRUD property.
     *
     * @param string $key
     * @return string|DashboardProperties
     */
    final public static function property(string $key = null)
    {
        if ($key === null) {
            return static::properties();
        }

        return array_get(static::properties()->toArray(), $key);
    }

    /**
     * Properties of CRUD controller.
     *
     * @return DashboardProperties
     */
    abstract protected static function properties(): DashboardProperties;

    /**
     * To init dashboard.
     *
     * @return void
     */
    final protected function initDashboard(): void
    {
        $this->init();
        $this->postInitCards('show');
        $this->sortCards();
    }

    /**
     * Using to override initialize.
     *
     * @return void
     */
    abstract protected function init(): void;

    public static function routes(): void
    {
        Route::name('dashboards.')->group(function () {
            Route::get(static::property('route'), static::class.'@show')->name(static::property('key'));
        });
    }

    public static function menus(): array
    {
        return [
            MenuItem::create(route('sanjab.dashboards.'.static::property('key')))
                    ->title(static::property('title'))
                    ->icon(static::property('icon'))
                    ->badge(static::property('badge'))
                    ->badgeVariant(static::property('badgeVariant'))
                    ->active(function () {
                        return Route::is('sanjab.dashboards.'.static::property('key'));
                    }),
        ];
    }

    public static function permissions(): array
    {
        $permission = PermissionItem::create(trans('sanjab::sanjab.dashboard'))
                        ->order(50)
                        ->addPermission(trans('sanjab::sanjab.access_to_admin_panel'), 'access_sanjab');

        return [$permission];
    }
}
