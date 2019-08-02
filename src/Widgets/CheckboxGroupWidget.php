<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Group check box widget
 *
 * @method $this    all(boolean $val)      has All button
 */
class CheckboxGroupWidget extends Widget
{
    protected $getters = [
        'options'
    ];

    protected $checkOptions = [];

    public function init(): void
    {
        $this->onIndex(false);
        $this->all(false);
        $this->tag("checkbox-group-widget");
        $this->viewTag("checkbox-group-view");
    }

    protected function store(Request $request, Model $item)
    {
        $item->{ $this->option("name") } = $request->input($this->option("name")) == "true";
    }

    protected function search(Builder $query, string $type = null, $search = null)
    {
        if ($search == "true") {
            $query->where($this->name, 1);
        }
        if ($search == "false") {
            $query->where($this->name, 0);
        }
    }

    /**
     * Add option to options
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
     * Add multiple options
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
}
