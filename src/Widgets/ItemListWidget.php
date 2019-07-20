<?php

namespace Sanjab\Widgets;

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
