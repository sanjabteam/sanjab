<?php

namespace Sanjab\Widgets\Relation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sanjab\Traits\SubWidgets;
use Sanjab\Widgets\SelectWidget;
use Sanjab\Widgets\Widget;
use stdClass;

/**
 * Belongs To Many with pivot items.
 */
class BelongsToManyWithPivotWidget extends BelongsToManyPickerWidget
{
    use SubWidgets;

    protected $getters = [
        'widgets',
    ];

    protected $values;

    public function init()
    {
        parent::init();
        $this->onIndex(false);
        $this->sortable(false);
        $this->tag('item-list-widget');
        $this->viewTag('item-list-view');
        $this->deleteConfirm(null);
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $responseItems = [];
        $items = $item->{ $this->property('name') };
        if ($items instanceof \Illuminate\Database\Eloquent\Model) {
            $items = [$items];
        }
        if (is_array($items) || $items instanceof Collection) {
            foreach ($items as $key => $itemModel) {
                if (! ($itemModel instanceof \Illuminate\Database\Eloquent\Model)) {
                    $itemModel = $this->arrayToModel($itemModel);
                }

                $responseObject = new stdClass;
                $responseObject->__id = $key;
                foreach ($this->widgets as $widget) {
                    $widget->doModifyResponse($responseObject, $itemModel->pivot);
                }
                $responseObject->{ $this->property('name').'_select' } = $itemModel->id;
                $responseItems[] = (array) $responseObject;
            }
        }
        $response->{ $this->property('name') } = $responseItems;
    }

    public function postInit()
    {
        parent::postInit();
        $relationWidget= SelectWidget::create($this->property('name').'_select', $this->property('title'))
                        ->optionsLabelKey('label')
                        ->relationKey('relatedKey')
                        ->withNull(null)
                        ->rules('required|distinct:strict|exists:'.$this->relatedModelTable.','.$this->relatedKey);
        foreach ($this->getOptions() as $option) {
            $relationWidget->addOption($option['value'], $option['label']);
        }
        array_unshift($this->widgets, $relationWidget);

        foreach ($this->widgets as $key => $widget) {
            $this->widgets[$key]->controllerProperties = $this->controllerProperties;
            $this->widgets[$key]->controllerProperties['model'] = $this->getRelatedModel();
            $this->widgets[$key]->postInit();
            $this->widgets[$key]->postInitSearchWidgets();
        }
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
                    $this->values[$key] = $relatedItems[$requestValue['__id']]->pivot;
                } else {
                    $this->values[$key] = new Pivot();
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
        $item->$name()->sync($this->mapPivotValues($this->values));
        foreach ($this->values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widget->doPostStore($widgetRequest, $this->values[$key]);
            }
        }
        $item->$name()->sync($this->mapPivotValues($this->values));
    }

    /**
     * Convert array request value to many to many with pivot format.
     *
     * @param array $values
     * @return array
     */
    protected function mapPivotValues(array $values)
    {
        $out = [];
        foreach ($values as $value) {
            $out[Arr::get($value, $this->property('name').'_select')] = Arr::except($value->toArray(), $this->property('name').'_select');
        }

        return $out;
    }

    /**
     * Add a widget for pivot to pivot widgets list.
     *
     * @param Widget $newWidget
     * @return $this
     */
    public function addPivot(Widget $newWidget)
    {
        $this->widgets[] = $newWidget;

        return $this;
    }
}
