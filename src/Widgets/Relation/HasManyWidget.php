<?php

namespace Sanjab\Widgets\Relation;

use Illuminate\Http\Request;
use Sanjab\Traits\SubWidgets;
use Illuminate\Database\Eloquent\Model;

/**
 * Input list of items with custom widgets.
 *
 * @method $this deleteConfirm(string $val)      if you want to show a confirm popup before delete set this to your message.
 */
class HasManyWidget extends RelationWidget
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

    protected function postStore(Request $request, Model $item)
    {
        $values = [];
        if (is_array($request->input($this->name))) {
            foreach ($request->input($this->name) as $key => $requestValue) {
                if (isset($requestValue['__id']) && ($item->{ $this->name } instanceof \Illuminate\Database\Eloquent\Collection) && isset($item->{ $this->name }[$requestValue['__id']])) {
                    $values[$key] = $item->{ $this->name }[$requestValue['__id']];
                } else {
                    $values[$key] = new $this->relatedModel;
                }
            }
        }

        foreach ($values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widget->doPreStore($widgetRequest, $values[$key]);
            }
            foreach ($this->widgets as $widget) {
                $widget->doStore($widgetRequest, $values[$key]);
            }
            foreach ($this->widgets as $widget) {
                $widget->doPostStore($widgetRequest, $values[$key]);
            }
        }
        $item->{ $this->name }()->whereNotIn(
            'id',
            array_values(array_filter(
                array_map(function ($ritem) {
                    return $ritem->id;
                }, $values),
                function ($rid) {
                    return $rid != null;
                }
            ))
        )->delete();
        $item->{ $this->name }()->saveMany($values);
    }
}
