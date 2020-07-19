<?php

namespace Sanjab\Widgets;

use Carbon\Carbon;

/**
 * Timezone select widget.
 */
class TimezoneWidget extends SelectWidget
{
    protected $selectOptions = [];

    public function init()
    {
        parent::init();
        foreach (timezone_identifiers_list() as $timezone) {
            $this->addOption($timezone, $timezone.' ('.Carbon::now($timezone)->getOffsetString().')');
        }
    }
}
