<?php

namespace Sanjab\Helpers;

use Illuminate\Support\Facades\App;

/**
 * @method $this title (string $value)     title of notification.
 * @method $this icon (string $value)      icon of notification.
 * @method $this hidden (callable $value)  callback to hide or show.
 * @method $this order (int $value)        order of menu item.
 * @method $this badge (string $badge)     set badge of menu item.
 */
class NotificationItem extends PropertiesHolder
{
    protected $properties = [
        'icon' => 'notifications',
        'order' => 100,
    ];

    protected $getters = [
        'items',
    ];

    /**
     * Items inside notification area.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Check menu item is hidden or not.
     *
     * @return bool
     */
    public function isHidden()
    {
        if (isset($this->properties['hidden'])) {
            return App::call($this->properties['hidden']);
        }

        return false;
    }

    /**
     * Add item to notification item.
     *
     * @param string $title
     * @param string $link
     * @param array $otherOptions
     * @return $this
     */
    public function addItem($title, $link = '#', array $otherOptions = [])
    {
        $this->items[] = array_merge($otherOptions, compact('title', 'link'));

        return $this;
    }

    /**
     * Add divider to items.
     *
     * @return $this
     */
    public function addDivider()
    {
        $this->items[] = 0;

        return $this;
    }

    /**
     * Get items inside notification.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * create new Menu item.
     *
     * @param  string|null  $icon
     *
     * @return static
     */
    public static function create(string $icon = null)
    {
        $out = new static;
        $icon && $out->icon($icon);

        return $out;
    }
}
