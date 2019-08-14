<?php

namespace Sanjab\Widgets;

use Illuminate\Database\Eloquent\Builder;

/**
 * Select widget
 */
class SelectWidget extends Widget
{
    protected $getters = [
        'options'
    ];

    protected $selectOptions = [];

    public function init()
    {
        $this->tag("select-widget");
        $this->indexTag("select-view")->viewTag('select-view');
    }

    public function postInit()
    {
        $this->rules('in:'.implode(",", array_keys($this->selectOptions)));
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
        $this->selectOptions[$key] = $title;
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
            $this->selectOptions[$key] = $title;
        }
        return $this;
    }

    protected function search(Builder $query, string $type = null, $search = null)
    {
        $filteredOptions = array_filter($this->selectOptions, function ($selectOption) use ($search) {
            return preg_match('/.*'.preg_quote($search).'.*/i', $selectOption);
        });
        if (count($filteredOptions) > 0) {
            switch ($type) {
                default:
                    $query->whereIn($this->property('name'), array_keys($filteredOptions));
                    break;
            }
        }
    }

    /**
     * Get select options
     *
     * @return array
     */
    public function getOptions()
    {
        $out = [];
        foreach ($this->selectOptions as $optionKey => $optionTitle) {
            $out[] = ['label' => $optionTitle, 'value' => $optionKey];
        }
        return $out;
    }
}
