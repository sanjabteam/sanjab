<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Sanjab\Models\TempModel;
use stdClass;

/**
 * Input list of items with custom widgets.
 */
class ItemListWidget extends Widget
{
    protected $getters = [
        'widgets'
    ];

    /**
     * Widgets.
     *
     * @var array|Widget[]
     */
    protected $widgets = [];

    public function init()
    {
        $this->onIndex(false);
        $this->searchable(false);
        $this->sortable(false);
        $this->tag("item-list-widget");
        $this->viewTag("item-list-view");
        $this->inputOptions(["type" => "text"]);
        $this->unique(true);
    }

    protected function preStore(Request $request, Model $item)
    {
        $values = $item->{$this->property('name')};
        if (! is_array($values)) {
            $values = [];
        }
        $values = $this->arraysToModels($request, $values);
        if (is_array($request->input($this->property('name')))) {
            foreach ($values as $key => $requestValues) {
                $widgetRequest = $this->widgetRequest($request, $key);
                foreach ($this->widgets as $widget) {
                    $widget->doPreStore($widgetRequest, $values[$key]);
                }
            }
        }
        $item->{$this->property('name')} = $this->modelsToArrays($values);
    }

    protected function store(Request $request, Model $item)
    {
        $values = $this->arraysToModels($request, $item->{$this->property('name')});
        if (is_array($request->input($this->property('name')))) {
            foreach ($values as $key => $requestValues) {
                $widgetRequest = $this->widgetRequest($request, $key);
                foreach ($this->widgets as $widget) {
                    $widget->doStore($widgetRequest, $values[$key]);
                }
            }
        }
        $item->{$this->property('name')} = $this->modelsToArrays($values);
    }

    protected function postStore(Request $request, Model $item)
    {
        $values = $this->arraysToModels($request, $item->{$this->property('name')});
        if (is_array($request->input($this->property('name')))) {
            foreach ($values as $key => $requestValues) {
                $widgetRequest = $this->widgetRequest($request, $key);
                foreach ($this->widgets as $widget) {
                    $widget->doPostStore($widgetRequest, $values[$key]);
                }
            }
        }
        $item->{$this->property('name')} = $this->modelsToArrays($values);
    }

    public function postInit()
    {
        foreach ($this->widgets as $widget) {
            $widget->postInit();
        }
    }

    public function validationRules($type): array
    {
        $rules = [
            $this->name => array_merge($this->property('rules.'.$type, []), ['array']),
        ];
        foreach ($this->widgets as $widget) {
            foreach ($widget->validationRules($type) as $ruleKey => $widgetRules) {
                $rules[$this->name.'.*.'.$ruleKey] = $widgetRules;
            }
        }
        return $rules;
    }

    public function validationAttributes(): array
    {
        $attributes = [
            $this->name         => $this->title,
            $this->name.'.*'    => $this->title,
        ];
        foreach ($this->widgets as $widget) {
            foreach ($widget->validationAttributes() as $attributeKey => $widgetAttributes) {
                $attributes[$this->name.'.*.'.$attributeKey] = $widgetAttributes.'('.$this->title.')';
            }
        }
        return $attributes;
    }

    public function validationMessages(): array
    {
        $messages = [];
        foreach ($this->widgets as $widget) {
            foreach ($widget->validationMessages() as $messageKey => $widgetMessages) {
                $messages[$this->name.'.*.'.$messageKey] = $widgetMessages.'('.$this->title.')';
            }
        }
        return $messages;
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $responseItems = [];
        foreach ($item->{ $this->property("name") } as $itemModel) {
            $itemModel = $this->arrayToModel($itemModel);
            $responseObject = new stdClass;
            foreach ($this->widgets as $widget) {
                $widget->doModifyResponse($responseObject, $itemModel);
            }
            $responseItems[] = (array)$responseObject;
        }
        $response->{ $this->property("name") } = $responseItems;
    }

    /**
     * Create request for widget.
     *
     * @param Request $request
     * @param int $index
     * @return Request
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
                if (isset($attributes[$key])) {
                    $models[] = $this->arrayToModel($attributes[$key]);
                }
            }
            return $models;
        }
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
     * Widgets getter
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
