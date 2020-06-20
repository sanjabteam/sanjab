<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Sanjab\Traits\SubWidgets;
use Illuminate\Database\Eloquent\Model;

/**
 * Input list of items with custom widgets.
 *
 * @method $this deleteConfirm(string $val)      if you want to show a confirm popup before delete set this to your message.
 * @method $this reversed(bool $val)             insert new item at begin instead of end. default is true.
 * @method $this draggable(bool $val)            is inserted items draggable or not.
 */
class ItemListWidget extends Widget
{
    use SubWidgets;

    protected $getters = [
        'widgets',
    ];

    public function init()
    {
        $this->onIndex(false);
        $this->searchable(false);
        $this->sortable(false);
        $this->tag('item-list-widget');
        $this->viewTag('item-list-view');
        $this->deleteConfirm(null);
    }

    protected function preStore(Request $request, Model $item)
    {
        $values = [];
        if (is_array($request->input($this->name))) {
            foreach ($request->input($this->name) as $key => $requestValue) {
                if (isset($requestValue['__id']) && is_array($item->{ $this->name }) && isset($item->{ $this->name }[$requestValue['__id']])) {
                    $values[$key] = $item->{ $this->name }[$requestValue['__id']];
                } else {
                    $values[$key] = [];
                }
            }
        }
        $values = $this->arraysToModels($request, $values);
        foreach ($values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widget->doPreStore($widgetRequest, $values[$key]);
            }
        }
        $item->{$this->property('name')} = $this->modelsToArrays($values);
    }

    protected function store(Request $request, Model $item)
    {
        $values = $this->arraysToModels($request, $item->{$this->property('name')});
        foreach ($values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widget->doStore($widgetRequest, $values[$key]);
            }
        }
        $item->{$this->property('name')} = $this->modelsToArrays($values);
    }

    protected function postStore(Request $request, Model $item)
    {
        $values = $this->arraysToModels($request, is_array($item->{$this->property('name')}) ? $item->{$this->property('name')} : []);
        foreach ($values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widget->doPostStore($widgetRequest, $values[$key]);
            }
        }
        $item->{$this->property('name')} = $this->modelsToArrays($values);
    }
}
