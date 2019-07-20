<?php

namespace Sanjab\Widgets;

/**
 * Input list of items
 *
 * @method $this unique(boolean $val)            should each item be unique or not.
 * @method $this inputOptions(array $val)        array of new item input attributes.
 * @method $this itemRules(array|string $val)    rules per item.
 */
class TextListWidget extends Widget
{
    public function init()
    {
        $this->onIndex(false);
        $this->searchable(false);
        $this->sortable(false);
        $this->tag("text-list-widget");
        $this->viewTag("text-list-view");
        $this->inputOptions(["type" => "text"]);
        $this->unique(true);
    }

    public function validationRules($type): array
    {
        $rules = is_string($this->property('itemRules')) ? explode('|', $this->property('itemRules')) : $this->property('itemRules', []);
        if ($this->property('unique')) {
            $rules = array_merge(['distinct'], $rules);
        }
        return [
            $this->name      => array_merge($this->property('rules.'.$type, []), ['array']),
            $this->name.'.*' => $rules,
        ];
    }
}
