<?php

namespace Sanjab\Traits;

use stdClass;
use Sanjab\Widgets\Widget;
use Illuminate\Http\Request;
use Sanjab\Models\TempModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Handle helper for Widgets that containing child widgets inside.
 */
trait SubWidgets
{
    /**
     * Widgets.
     *
     * @var array|Widget[]
     */
    protected $widgets = [];

    public function postInit()
    {
        foreach ($this->widgets as $widget) {
            $widget->postInit();
        }
    }

    public function validationRules(Request $request, string $type, Model $item = null): array
    {
        $rules = [
            $this->name => array_merge($this->property('rules.'.$type, []), ['array']),
        ];

        $values = optional($item)->{$this->property('name')} instanceof Collection ?
                    $item->{$this->property('name')} :
                    $this->arraysToModels($request, is_array(optional($item)->{$this->property('name')}) ? $item->{$this->property('name')} : []);

        foreach ($values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widgetRequest = $this->widgetRequest($request, $key);
                foreach ($widget->validationRules($widgetRequest, $type, $item) as $ruleKey => $widgetRules) {
                    $rules[$this->name.'.'.$key.'.'.$ruleKey] = $widgetRules;
                }
            }
        }

        return $rules;
    }

    public function validationAttributes(Request $request, string $type, Model $item = null): array
    {
        $attributes = [
            $this->name         => $this->title,
            $this->name.'.*'    => $this->title,
        ];

        $values = optional($item)->{$this->property('name')} instanceof Collection ?
                    $item->{$this->property('name')} :
                    $this->arraysToModels($request, is_array(optional($item)->{$this->property('name')}) ? $item->{$this->property('name')} : []);

        foreach ($values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widgetRequest = $this->widgetRequest($request, $key);
                foreach ($widget->validationAttributes($widgetRequest, $type, $item) as $attributeKey => $widgetAttributes) {
                    $attributes[$this->name.'.'.$key.'.'.$attributeKey] = $widgetAttributes.'('.$this->title.')';
                }
            }
        }

        return $attributes;
    }

    public function validationMessages(Request $request, string $type, Model $item = null): array
    {
        $messages = [];

        $values = optional($item)->{$this->property('name')} instanceof Collection ?
                    $item->{$this->property('name')} :
                    $this->arraysToModels($request, is_array(optional($item)->{$this->property('name')}) ? $item->{$this->property('name')} : []);

        foreach ($values as $key => $requestValues) {
            foreach ($this->widgets as $widget) {
                $widgetRequest = $this->widgetRequest($request, $key);
                foreach ($widget->validationMessages($widgetRequest, $type, $item) as $messageKey => $widgetMessages) {
                    $messages[$this->name.'.'.$key.'.'.$messageKey] = $widgetMessages.'('.$this->title.')';
                }
            }
        }

        return $messages;
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $responseItems = [];
        if (is_array($item->{ $this->property('name') }) || $item->{ $this->property('name') } instanceof Collection) {
            foreach ($item->{ $this->property('name') } as $key => $itemModel) {
                if (! ($itemModel instanceof \Illuminate\Database\Eloquent\Model)) {
                    $itemModel = $this->arrayToModel($itemModel);
                }

                $responseObject = new stdClass;
                $responseObject->__id = $key;
                foreach ($this->widgets as $widget) {
                    $widget->doModifyResponse($responseObject, $itemModel);
                }
                $responseItems[] = (array) $responseObject;
            }
        }
        $response->{ $this->property('name') } = $responseItems;
    }

    protected function modifyRequest(Request $request, Model $item = null)
    {
        $values = [];
        if (is_array($request->input($this->name))) {
            foreach ($request->input($this->name) as $key => $requestValue) {
                if (isset($requestValue['__id']) &&
                    $item &&
                    (is_array($item->{ $this->name }) || $item->{ $this->name } instanceof Collection)
                    && isset($item->{ $this->name }[$requestValue['__id']])
                ) {
                    $values[$key] = $item->{ $this->name }[$requestValue['__id']];
                } else {
                    $values[$key] = [];
                }
            }
        }

        $values = $this->arraysToModels($request, $values);
        $editedRequest = $request->input($this->property('name'));
        foreach ($values as $key => $requestValues) {
            $widgetRequest = $this->widgetRequest($request, $key);
            foreach ($this->widgets as $widget) {
                $widget->doModifyRequest($widgetRequest, $requestValues);
            }
            $editedRequest[$key] = $widgetRequest->all();
        }
        $editedRequest = $editedRequest;
        $request->merge([$this->property('name') => $editedRequest]);
    }

    /**
     * Create request for widget.
     *
     * @param Request $request
     * @param int $index
     * @param Model|null $item
     * @return Request|null
     */
    protected function widgetRequest(Request $request, int $index)
    {
        $widgetRequest = Request::create(
            $request->getUri(),
            $request->method(),
            is_array($request->input($this->name.'.'.$index)) ? $request->input($this->name.'.'.$index) : [],
            $request->cookie(),
            is_array($request->file($this->name.'.'.$index)) ? $request->file($this->name.'.'.$index) : [],
            $request->server()
        );
        $widgetRequest->replace(is_array($request->input($this->name.'.'.$index)) ? $request->input($this->name.'.'.$index) : []);

        return $widgetRequest;
    }

    /**
     * Convert array of arrays to array of models.
     *
     * @param Request $request
     * @param array $attributes
     * @return array|Model[]
     */
    protected function arraysToModels(Request $request, array $attributes)
    {
        $models = [];
        if (is_array($request->input($this->property('name')))) {
            foreach ($request->input($this->property('name')) as $key => $requestValues) {
                if (is_array($requestValues) && isset($attributes[$key])) {
                    if ($attributes[$key] instanceof Model) {
                        $models[$key] = $attributes[$key];
                    } else {
                        $models[$key] = $this->arrayToModel($attributes[$key]);
                    }
                } else {
                    $models[$key] = new TempModel();
                }
            }
        }

        return $models;
    }

    /**
     * Convert array to model.
     *
     * @param array $attributes
     * @return Model
     */
    protected function arrayToModel(array $attributes)
    {
        $model = new TempModel();
        $model->setCasts(array_map(function ($widget) {
            return $widget->property('name');
        }, $this->widgets));
        foreach ($attributes as $key2 => $value) {
            $model->{ $key2 } = $value;
        }

        return $model;
    }

    /**
     * Convert models to arrays.
     *
     * @param array|Model[] $items
     * @return array
     */
    protected function modelsToArrays(array $items)
    {
        return array_map(function ($item) {
            return $item->toArray();
        }, $items);
    }

    /**
     * Widgets getter.
     *
     * @return array
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * Add a widget to widgets list.
     *
     * @param Widget $newWidget
     * @return $this
     */
    public function addWidget(Widget $newWidget)
    {
        $this->widgets[] = $newWidget;

        return $this;
    }

    /**
     * Add multiple widgets to widgets list.
     *
     * @param array $newWidget
     * @return $this
     */
    public function addWidgets(array $newWidgets)
    {
        $this->widgets = array_merge($this->widgets, $newWidgets);

        return $this;
    }
}
