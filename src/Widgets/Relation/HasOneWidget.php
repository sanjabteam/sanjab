<?php

namespace Sanjab\Widgets\Relation;

/**
 * Input list of an item with custom widgets.
 */
class HasOneWidget extends HasManyWidget
{
    public function init()
    {
        parent::init();
        $this->max(1);
    }
}
