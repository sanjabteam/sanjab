<?php

namespace Sanjab\Widgets;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use stdClass;

/**
 * @method $this auto (boolean $val)                        Auto continue/close on select.
 * @method $this weekStart (integer $val)                   First day of the week. 1 is Monday and 7 is Sunday.
 * @method $this hourStep (integer $val)                    Hour step.
 * @method $this minuteStep (integer $val)                  Minute step.
 */
class DateTimeWidget extends Widget
{
    public function init()
    {
        $this->tag('datetime');
        $this->setProperty('floatlabel', true);
        $this->setProperty('input-class', 'form-control');
        $this->setProperty('type', 'datetime');
        $this->setProperty('value-zone', 'local');
        $this->rules('date');
        $this->setProperty('phrases', ['ok' => trans('sanjab::sanjab.ok'), 'cancel' => trans('sanjab::sanjab.cancel')]);
        $this->setProperty('format', ['year' => 'numeric', 'month' => 'short', 'day' => 'numeric', 'hour' => 'numeric', 'minute' => 'numeric', 'hour12' => false]);
    }

    protected function store(Request $request, Model $item)
    {
        $item->{ $this->property('name') } = Carbon::parse($request->input($this->property('name')));
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        if (in_array(Arr::get($this->controllerProperties, 'type'), ['index', 'show']) && $item->{ $this->property('name') } instanceof Carbon) {
            if ($this->property('type') == 'datetime') {
                $response->{ $this->property('name') } = $item->{ $this->property('name') }->toFormattedDateString().' '.$item->{ $this->property('name') }->toTimeString();
            } elseif ($this->property('type') == 'date') {
                $response->{ $this->property('name') } = $item->{ $this->property('name') }->toFormattedDateString();
            } else {
                $response->{ $this->property('name') } = $item->{ $this->property('name') }->toTimeString();
            }
        } else {
            $response->{ $this->property('name') } = $item->{ $this->property('name') };
        }
    }

    /**
     * Time only input.
     *
     * @return $this
     */
    public function timeOnly()
    {
        $this->setProperty('type', 'time');
        $this->setProperty('format', ['hour' => 'numeric', 'minute' => 'numeric', 'hour12' => false]);

        return $this;
    }

    /**
     * Date only input.
     *
     * @return $this
     */
    public function dateOnly()
    {
        $this->setProperty('type', 'date');
        $this->setProperty('format', ['year' => 'numeric', 'month' => 'short', 'day' => 'numeric', 'hour12' => false]);

        return $this;
    }

    /**
     * Format time as 12 hour.
     *
     * @param bool $val
     * @return $this
     */
    public function use12HourFormat(bool $val = true)
    {
        $this->setProperty('format', array_merge($this->property('format'), ['hour12' => $val]));
        $this->setProperty('use12-hour', $val);

        return $this;
    }

    /**
     * Minimum date time.
     *
     * @param Carbon $val
     * @return $this
     */
    public function minDateTime(Carbon $val)
    {
        $this->setProperty('min-datetime', $val->toIso8601String());
        $this->rules('after_or_equal:'.$val->toFormattedDateString().' '.$val->toTimeString());

        return $this;
    }

    /**
     * Maximum date time.
     *
     * @param Carbon $val
     * @return $this
     */
    public function maxDateTime(Carbon $val)
    {
        $this->setProperty('max-datetime', $val->toIso8601String());
        $this->rules('before_or_equal:'.$val->toFormattedDateString().' '.$val->toTimeString());

        return $this;
    }
}
