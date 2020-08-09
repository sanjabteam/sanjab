<?php

namespace Sanjab\Helpers;

use Illuminate\Support\Str;

/**
 * Holder of Setting controller properties.
 *
 * @method $this key (string $val)                   Route and group key of settings.
 * @method $this title (string $val)                 Title of setting.
 * @method $this description (string $val)           short description about setting.
 * @method $this icon (string $val)                  Icon of setting.
 * @method $this globalSearch (bool $val)            Should be present in global search or not.
 * @method $this badgeVariant (string $val)          menu badge bootstrap variant.
 */
class SettingProperties extends PropertiesHolder
{
    protected $properties = [
        'icon' => 'settings',
        'globalSearch' => true,
        'badgeVariant' => 'danger',
    ];

    /**
     * create new Menu item.
     *
     * @param  null  $key
     *
     * @return static
     */
    public static function create($key = null)
    {
        $out = new static;
        if ($key) {
            $out->key($key);
        }
        $out->title(Str::singular(Str::title($key)));

        return $out;
    }

    /**
     * Badge to show beside menu.
     *
     * @param  callable  $badgeCallback
     *
     * @return $this
     */
    public function badge(callable $badgeCallback)
    {
        $this->setProperty('badge', $badgeCallback);

        return $this;
    }
}
