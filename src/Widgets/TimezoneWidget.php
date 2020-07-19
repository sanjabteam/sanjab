<?php

namespace Sanjab\Widgets;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Sanjab\Helpers\SearchType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
