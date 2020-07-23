<?php

namespace Sanjab\Plugins\Notification;

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
    /**
     * Notification items.
     *
     * @var NotificationItem[]
     */
    protected static $notificationItems = null;

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
     * @property string $icon  icon
     * @return static
     */
    public static function create(string $icon = null)
    {
        $out = new static;
        if ($icon) {
            $out->icon($icon);
        }

        return $out;
    }

    /**
     * All controllers menu items.
     *
     * @param bool $forceRefresh  force use lastest version instead of cached data.
     * @return NotificationItem[]
     * @throws Exception
     */
    public static function get($forceRefresh = false): array
    {
        if (! Auth::check()) {
            return [];
        }
        if (! (static::$notificationItems == null || $forceRefresh)) {
            return static::$notificationItems;
        }
        static::$notificationItems = [];
        foreach (static::controllers() as $controller) {
            foreach ($controller::notifications() as $notificationItem) {
                if (! $notificationItem instanceof NotificationItem) {
                    throw new Exception("Some permission item in '$controller' is not a NotificationItem type.");
                }
                static::$notificationItems[] = $notificationItem;
            }
        }
        static::$notificationItems = array_filter(static::$notificationItems, function ($notificationItem) {
            return ! $notificationItem->isHidden();
        });
        usort(static::$notificationItems, function ($a, $b) {
            return $a->order > $b->order;
        });

        return static::$notificationItems;
    }
}
