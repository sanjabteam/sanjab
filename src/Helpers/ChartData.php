<?php

namespace Sanjab\Helpers;

/**
 * @method $this label(string $val)         label for data.
 * @method $this data(array|callable $val)  data array/callback.
 */
class ChartData extends PropertiesHolder
{
    /**
     * Create new chart data.
     *
     * @param string $label
     * @return static
     */
    public static function create($label = null)
    {
        $out = new static;
        if ($label) {
            $out->label($label);
        }

        return $out;
    }

    /**
     * Background color.
     *
     * @param string|array $color
     * @return $this
     */
    public function color($color)
    {
        $this->setProperty('backgroundColor', $color);
        return $this;
    }
}
