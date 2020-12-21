<?php

namespace Sanjab\Widgets\Relation;

use Illuminate\Http\Request;
use Sanjab\Traits\SubWidgets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    protected $values;

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
        $this->values = [];

        $name = $this->name;
        $relatedItems = $item->$name;
        if ($relatedItems instanceof Model) {
            $relatedItems = collect([$relatedItems]);
        }
        if (is_array($request->input($name))) {
            foreach ($request->input($name) as $key => $requestValue) {
                if (isset($requestValue['__id']) && ($relatedItems instanceof Collection) && isset($relatedItems[$requestValue['__id']])) {
                    $this->values[$key] = $relatedItems[$requestValue['__id']];
                } else {
                    $this->values[$key] = new $this->relatedModel;
                }
            }
        }

        foreach ($this->values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widget->doPreStore($widgetRequest, $this->values[$key]);
            }
        }
    }

    protected function postStore(Request $request, Model $item)
    {
        $name = $this->name;
        foreach ($this->values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widget->doStore($widgetRequest, $this->values[$key]);
            }
        }
        $item->$name()->saveMany($this->values);
        foreach ($this->values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widget->doPostStore($widgetRequest, $this->values[$key]);
            }
        }
        $item->$name()->saveMany($this->values);
        $item->$name()->whereNotIn(
            'id',
            array_values(array_filter(
                array_map(function ($ritem) {
                    return $ritem->id;
                }, $this->values),
                function ($rid) {
                    return $rid != null;
                }
            ))
        )->delete();
    }

    public function postInit()
    {
        foreach ($this->widgets as $key => $widget) {
            $this->widgets[$key]->controllerProperties = $this->controllerProperties;
            $this->widgets[$key]->controllerProperties['model'] = $this->getRelatedModel();
            $this->widgets[$key]->postInit();
            $this->widgets[$key]->postInitSearchWidgets();
        }
    }
}
