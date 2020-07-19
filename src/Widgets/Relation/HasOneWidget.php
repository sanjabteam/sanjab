<?php

namespace Sanjab\Widgets\Relation;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

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
