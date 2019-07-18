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
 */
class SettingProperties extends PropertiesHolder
{
    protected $properties = [
        'icon' => 'settings'
    ];

    /**
     * create new Menu item
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
}
