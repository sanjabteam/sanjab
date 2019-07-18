<?php

namespace Sanjab\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

/**
 * @method $this url (string $value)       url of menu item.
 * @method $this title (string $value)     title of menu.
 * @method $this icon (string $value)      icon of menu.
 * @method $this active (callable $value)  callback to check this item is active or not.
 * @method $this hidden (callable $value)  callback to hide or show.
 * @method $this target (string $value)    menu target.
 * @method $this order (int $value)        order of menu item.
 */
class MenuItem extends PropertiesHolder
{
    protected $properties = [
        'icon' => 'code',
        'title' => 'TITLE HERE',
        'order' => 100
    ];

    /**
     * Children inside menu item.
     *
     * @var MenuItem[]
     */
    protected $children = [];

    /**
     * Check menu item is active or not.
     *
     * @return boolean
     */
    public function isActive()
    {
        if (isset($this->properties['active'])) {
            return App::call($this->properties['active']);
        }
        if (isset($this->properties['url'])) {
            return Request::is(trim(parse_url($this->properties['url'])['path'], '/'));
        }
    }

    /**
     * Check menu item is hidden or not.
     *
     * @return boolean
     */
    public function isHidden()
    {
        if (isset($this->properties['hidden'])) {
            return App::call($this->properties['hidden']);
        }
        return false;
    }

    /**
     * Get children of item.
     *
     * @return array|MenuItem[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Check has children.
     *
     * @return bool
     */
    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    /**
     * Add a child item inside.
     *
     * @param MenuItem $childItem
     * @return $this
     */
    public function addChild(MenuItem $childItem)
    {
        $this->children[] = $childItem;
        return $this;
    }

    /**
     * Add multiple child
     *
     * @param array|MenuItem[]  $items
     * @return $this
     */
    public function addChildren(array $childItems)
    {
        $this->children = array_merge($this->children, $childItems);
        return $this;
    }

    /**
     * create new Menu item
     *
     * @property string $url  url of menu item.
     * @return static
     */
    public static function create($url = null)
    {
        $out = new static;
        if ($url) {
            $out->url($url);
        }

        return $out;
    }
}
