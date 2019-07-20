<?php

namespace Sanjab\Widgets\Relation;

use stdClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Exception;

/**
 * Belongs to many select box.
 *
 * @method $this    max(integer $val)                   maximum count of related.
 * @method $this    ajax(bool $val)                     should this work with ajax.
 * @method $this    ajaxController(string $val)         controller holding widget working with ajax options.
 * @method $this    ajaxControllerAction(string $val)   controller action working with ajax options.
 * @method $this    ajaxControllerItem(string $val)     controller action parameter working with ajax options.
 */
class BelongsToManyPickerWidget extends RelationWidget
{
    protected $getters = [
        'options',
        'controller',
        'controllerAction',
        'controllerItem',
    ];

    public function init()
    {
        parent::init();
        $this->onIndex(false);
        $this->sortable(false);
        $this->ajax(false);
        $this->tag("belongs-to-picker-widget");
        $this->viewTag("belongs-to-many-picker-view");
        $this->setProperty("multiple", true);
        $this->setProperty("tagging", true);
        $this->relationKey('relatedKey');
    }

    public function getController()
    {
        if ($this->property('ajax') && isset($this->controllerProperties['controller']) == false && empty($this->property('ajaxController'))) {
            throw new Exception("Please set ajax controller for '".$this->property('name')."'");
        }
        if (isset($this->controllerProperties['controller'])) {
            return $this->controllerProperties['controller'];
        }
        return $this->property('ajaxController');
    }

    public function getControllerAction()
    {
        if ($this->property('ajax') && isset($this->controllerProperties['type']) == false && empty($this->property('ajaxControllerAction'))) {
            throw new Exception("Please set ajax controller action for '".$this->property('name')."'");
        }
        if (isset($this->controllerProperties['type'])) {
            return $this->controllerProperties['type'];
        }
        return $this->property('ajaxControllerAction');
    }

    public function getControllerItem()
    {
        if (isset($this->controllerProperties['item'])) {
            return optional($this->controllerProperties['item'])->id;
        }
        return optional($this->property('ajaxControllerItem'))->id;
    }

    protected function postStore(Request $request, Model $item)
    {
        if (is_array($request->input($this->property("name")))) {
            $item->{ $this->property("name") }()->sync($request->input($this->property("name")));
        }
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $response->{ $this->property("name") } = $item->{ $this->property("name") }()->get()->pluck($this->relatedKey)->toArray();
    }

    public function validationRules($type): array
    {
        $arrayRule = ['array'];
        if ($this->property("max")) {
            $arrayRule[] = 'max:'.$this->property("max");
        }
        return [
            $this->property("name")       => array_merge($arrayRule, $this->property('rules.'.$type, [])),
            $this->property("name").".*"  => ['numeric', 'exists:'.$this->relatedModelTable.",".$this->relatedKey],
        ];
    }

    public function getOptions()
    {
        if ($this->property('ajax') && $this->controllerProperties['type'] != 'show') {
            return [];
        }
        return parent::getOptions();
    }
}
