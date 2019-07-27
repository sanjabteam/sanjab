<?php

namespace Sanjab\Helpers;

use Sanjab\Widgets\Widget;

/**
 * @method $this type(string $val)      type of search.
 * @method $this title(string $val)     title of type.
 */
class SearchType extends PropertiesHolder
{
    protected $getters = [
        'widgets'
    ];
    /**
     * Search Widgets.
     *
     * @var array|Widget
     */
    protected $widgets = [];

    /**
     * Add widget to search type.
     *
     * @param Widget $widget
     * @return $this
     */
    public function addWidget(Widget $widget)
    {
        $widget->setProperty('searchable', false);
        $this->widgets[] = $widget;
        return $this;
    }

    /**
     * Get widgets of search type.
     *
     * @return array
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * create new Search type.
     *
     * @property string $icon  icon
     * @return static
     */
    public static function create(string $type = null, string $title = null)
    {
        $out = new static;
        if ($type) {
            $out->type($type);
        }
        if ($title) {
            $out->title($title);
        }

        return $out;
    }
}
