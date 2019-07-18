<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;

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
        $this->rules('array');
    }

    public function validationRules($type)
    {
        $rules = is_string($this->property('itemRules')) ? explode('|', $this->property('itemRules')) : $this->property('itemRules', []);
        if ($this->property('unique')) {
            $rules = array_merge(['distinct'], $rules);
        }
        return [
            $this->name      => $this->property('rules.'.$type, []),
            $this->name.'.*' => $rules,
        ];
    }
}
