<?php

namespace Sanjab\Tests\Controllers;

use Sanjab;
use Sanjab\Helpers\DashboardProperties;
use Sanjab\Controllers\DashboardController as SanjabDashboardController;

class DashboardController extends SanjabDashboardController
{
    protected static function properties(): DashboardProperties
    {
        return DashboardProperties::create()
                ->title(trans('sanjab::sanjab.dashboard'));
    }

    protected function init(): void
    {
        $this->cards = Sanjab::dashboardCards();
    }
}
