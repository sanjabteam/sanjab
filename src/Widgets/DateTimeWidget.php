<?php

namespace Sanjab\Widgets;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Sanjab\Helpers\SearchType;
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

    /**
     * Get search types.
     *
     * @return array|SearchType[]
     */
    protected function searchTypes(): array
    {
        return [
            SearchType::create('empty', trans('sanjab::sanjab.is_empty')),
            SearchType::create('not_empty', trans('sanjab::sanjab.is_not_empty')),
            SearchType::create('equal', trans('sanjab::sanjab.equal'))
                        ->addWidget(
                            $this->copy()
                                ->title(trans('sanjab::sanjab.equal'))
                        ),
            SearchType::create('not_equal', trans('sanjab::sanjab.not_equal'))
                        ->addWidget(
                            $this->copy()
                                ->title(trans('sanjab::sanjab.not_equal'))
                        ),
            SearchType::create('more', trans('sanjab::sanjab.more'))
                        ->addWidget(
                            $this->copy()
                                ->title(trans('sanjab::sanjab.more'))
                        ),
            SearchType::create('more_or_eqaul', trans('sanjab::sanjab.more_or_eqaul'))
                        ->addWidget(
                            $this->copy()
                                ->title(trans('sanjab::sanjab.more_or_eqaul'))
                        ),
            SearchType::create('less', trans('sanjab::sanjab.less'))
                        ->addWidget(
                            $this->copy()
                                ->title(trans('sanjab::sanjab.less'))
                        ),
            SearchType::create('less_or_eqaul', trans('sanjab::sanjab.less_or_eqaul'))
                        ->addWidget(
                            $this->copy()
                                ->title(trans('sanjab::sanjab.less_or_eqaul'))
                        ),
            SearchType::create('between', trans('sanjab::sanjab.between'))
                        ->addWidget(
                            $this->copy()
                                ->name('first')
                                ->title(trans('sanjab::sanjab.between'))
                        )
                        ->addWidget(
                            $this->copy()
                                ->name('second')
                                ->title(trans('sanjab::sanjab.between'))
                        ),
            SearchType::create('not_between', trans('sanjab::sanjab.not_between'))
                        ->addWidget(
                            $this->copy()
                                ->name('first')
                                ->title(trans('sanjab::sanjab.not_between'))
                        )
                        ->addWidget(
                            $this->copy()
                                ->name('second')
                                ->title(trans('sanjab::sanjab.not_between'))
                        ),
        ];
    }

    /**
     * To override search query modify.
     *
     * @param Builder $query
     * @param string $type
     * @param mixed $search
     * @return void
     */
    protected function search(Builder $query, string $type = null, $search = null)
    {
        $name = $this->property('name');
        if (is_array($search)) {
            foreach ($search as $key => $value) {
                try {
                    $search[$key] = Carbon::parse($value);
                    if ($this->property('time')) {
                        $search[$key]->setTime($search[$key]->format('H'), $search[$key]->format('i'));
                    }
                } catch (\Exception $exception) {
                    return;
                }
            }
            if ($this->property('time')) {
                $search['second']->modify('+59 seconds');
            } else {
                $search['second']->modify('+23 hours +59 minutes +59 seconds');
            }
        } else {
            try {
                $search = Carbon::parse($search);
                if ($this->property('time')) {
                    $search->setTime($search->format('H'), $search->format('i'));
                }
                if (in_array($type, ['equal', 'not_equal'])) {
                    $search = ['first' => $search];
                    $search['second'] = clone $search['first'];
                    if ($this->property('time')) {
                        $search['second']->modify('+59 seconds');
                    } else {
                        $search['second']->modify('+23 hours +59 minutes +59 seconds');
                    }
                    $search = array_values($search);
                }
            } catch (\Exception $exception) {
                return;
            }
        }
        switch ($type) {
            case 'empty':
                $query->whereNull($name);
                break;
            case 'not_empty':
                $query->whereNotNull($name);
                break;
            case 'equal':
                $query->whereBetween($name, $search);
                break;
            case 'not_equal':
                $query->whereNotBetween($name, $search);
                break;
            case 'more':
                $query->where($name, '>', $search);
                break;
            case 'more_or_eqaul':
                $query->where($name, '>=', $search);
                break;
            case 'less':
                $query->where($name, '<', $search);
                break;
            case 'less_or_eqaul':
                $query->where($name, '<=', $search);
                break;
            case 'between':
                $query->whereBetween($name, [$search['first'] < $search['second'] ? $search['first'] : $search['second'], $search['first'] < $search['second'] ? $search['second'] : $search['first']]);
                break;
            case 'not_between':
                $query->whereNotBetween($name, [$search['first'] < $search['second'] ? $search['first'] : $search['second'], $search['first'] < $search['second'] ? $search['second'] : $search['first']]);
                break;
            default:
                if (intval($search->format('Y')) >= 1900 && intval($search->format('Y')) <= 2200) {
                    if ($this->property('time')) {
                        $query->where($name, '=', $search);
                    } else {
                        $query->whereDate($name, '=', $search);
                    }
                }
                break;
        }
    }
}
