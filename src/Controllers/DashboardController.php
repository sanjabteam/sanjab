<?php

namespace Sanjab\Controllers;

use stdClass;
use Sanjab\Helpers\MenuItem;
use Sanjab\Helpers\PermissionItem;
use Illuminate\Support\Facades\Route;
use Sanjab\Helpers\DashboardProperties;

/**
 * Base controller for dashboard classes.
 */
abstract class DashboardController extends SanjabController
{
    /**
     * Dashboard cards.
     *
     * @var array|\Sanjab\Cards\Card
     */
    protected $cards = [];

    /**
     * Show dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $this->initDashboard();
        $cardsData = [];
        foreach ($this->cards as $key => $card) {
            $cardsData[$key] = new stdClass;
            $card->doModifyResponse($cardsData[$key]);
        }
        return view('sanjab::dashboard', [
            'properties' => $this->properties(),
            'cards' => $this->cards,
            'cardsData' => $cardsData
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
        foreach ($this->cards as $card) {
            $card->postInit();
        }
    }

    /**
     * Using to override initialize.
     *
     * @return void
     */
    abstract protected function init(): void;

    public static function routes(): void
    {
        Route::name("dashboards.")->group(function () {
            Route::get(static::property('route'), static::class.'@show')->name(static::property('key'));
        });
    }

    public static function menus(): array
    {
        return [
            MenuItem::create(route('sanjab.dashboards.'.static::property('key')))
                    ->title(static::property('title'))
                    ->icon(static::property('icon'))
                    ->active(function () {
                        return Route::is('sanjab.dashboards.'.static::property('key'));
                    })
        ];
    }

    public static function permissions(): array
    {
        $permission = PermissionItem::create(trans('sanjab::sanjab.dashboard'))
                        ->addPermission(trans('sanjab::sanjab.access_to_admin_panel'), 'access_sanjab');
        return [$permission];
    }
}
