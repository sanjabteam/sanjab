<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Input list of items.
 *
 * @method $this unique(boolean $val)            should each item be unique or not.
 * @method $this inputOptions(array $val)        array of new item input attributes.
 * @method $this itemRules(array|string $val)    rules per item.
 * @method $this deleteConfirm(string $val)      if you want to show a confirm popup before delete set this to your message.
 * @method $this reversed(bool $val)             insert new item at begin instead of end. default is true.
 * @method $this draggable(bool $val)            is inserted items draggable or not.
 */
class TextListWidget extends Widget
{
    public function init()
    {
        $this->onIndex(false);
        $this->searchable(false);
        $this->sortable(false);
        $this->tag('text-list-widget');
        $this->viewTag('text-list-view');
        $this->inputOptions(['type' => 'text']);
        $this->unique(true);
        $this->deleteConfirm(null);
    }

    public function validationRules(Request $request, string $type, Model $item = null): array
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
