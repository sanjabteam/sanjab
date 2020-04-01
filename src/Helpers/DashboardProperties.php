<?php

namespace Sanjab\Helpers;

/**
 * Holder of Dashboard properties.
 *
 * @method $this key (string $val)          Dashboard unique key.
 * @method $this route (string $val)        Path of dashboard.
 * @method $this title (string $val)        Title of dashboard.
 * @method $this description (string $val)  Description about dashboard.
 * @method $this badgeVariant (string $val) menu badge bootstrap variant.
 */
class DashboardProperties extends PropertiesHolder
{
    protected $properties = [
        'icon'          => 'dashboard',
        'key'           => 'dashboard',
        'description'   => '',
        'badgeVariant'  => 'danger',
    ];

    /**
     * create new Menu item.
     *
     * @return static
     */
    public static function create($route = '/')
    {
        $out = new static;
        if ($route) {
            $out->route($route);
        }

        return $out;
    }

    /**
     * Badge to show beside menu.
     *
     * @param callable $callback
     * @return $this
     */
    public function badge(callable $badgeCallback)
    {
        $this->setProperty('badge', $badgeCallback);

        return $this;
    }
}
