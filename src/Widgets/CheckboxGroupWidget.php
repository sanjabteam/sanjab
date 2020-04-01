<?php

namespace Sanjab\Widgets;

use stdClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Group check box widget.
 *
 * @method $this    all(boolean $val)      has All button
 */
class CheckboxGroupWidget extends Widget
{
    protected $getters = [
        'options',
    ];

    protected $checkOptions = [];

    public function init(): void
    {
        $this->searchable(false);
        $this->onIndex(false);
        $this->all(false);
        $this->tag('checkbox-group-widget');
        $this->viewTag('checkbox-group-view');
    }

    protected function store(Request $request, Model $item)
    {
        $values = array_combine(array_keys($this->checkOptions), array_fill(0, count($this->checkOptions), false));
        if (is_array($request->input($this->property('name')))) {
            foreach ($request->input($this->property('name')) as $checked) {
                if (isset($values[$checked])) {
                    $values[$checked] = true;
                }
            }
        }
        $item->{ $this->property('name') } = $values;
    }

    protected function search(Builder $query, string $type = null, $search = null)
    {
        if ($search == 'true') {
            $query->where($this->name, 1);
        }
        if ($search == 'false') {
            $query->where($this->name, 0);
        }
    }

    /**
     * Add option to options.
     *
     * @param mixed $key
     * @param string $title
     * @return $this
     */
    public function addOption($key, string $title)
    {
        $this->checkOptions[$key] = $title;

        return $this;
    }

    /**
     * Add multiple options.
     *
     * @param array $options
     * @return $this
     */
    public function addOptions(array $options)
    {
        foreach ($options as $key => $title) {
            $this->checkOptions[$key] = $title;
        }

        return $this;
    }

    /**
     * Options.
     *
     * @return array
     */
    public function getOptions()
    {
        $out = [];
        foreach ($this->checkOptions as $optionKey => $optionTitle) {
            $out[] = ['text' => $optionTitle, 'value' => $optionKey];
        }

        return $out;
    }

    /**
     * To modifying model response.
     *
     * @param object $respones
     * @param Model $item
     * @return void
     */
    protected function modifyResponse(stdClass $response, Model $item)
    {
        $values = [];
        if (is_array($item->{ $this->property('name') })) {
            foreach ($item->{ $this->property('name') } as $name => $value) {
                if ($value) {
                    $values[] = $name;
                }
            }
        }
        $response->{ $this->property('name') } = $values;
    }
}
