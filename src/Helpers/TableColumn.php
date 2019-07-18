<?php

namespace Sanjab\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

/**
 * @method $this key (string $value)       Key of response item.
 * @method $this sortable (boolean $value) Is column sortable.
 * @method $this tag(string $value)        Tag using to show.
 */
class TableColumn extends PropertiesHolder
{
    public function __construct($properties = [])
    {
        parent::__construct($properties);
        $this->sortable(true);
    }

    /**
     * Title of table column.
     *
     * @param string $title
     * @return $this
     */
    public function title(string $title)
    {
        $this->properties['label'] = $title;
        return $this;
    }

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

        return $out;
    }
}
