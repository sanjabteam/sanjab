<?php

namespace Sanjab\Widgets\Relation;

use stdClass;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Belongs To Many with checkbox.
 *
 * @method $this    all(boolean $val)      has All button
 */
class BelongsToManyWidget extends BelongsToManyPickerWidget
{
    public function init()
    {
        parent::init();
        $this->all(false);
        $this->tag("checkbox-group-widget");
        $this->optionsLabelKey('text');
    }
}
