<?php

namespace Sanjab\Widgets\Relation;

use stdClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Belongs to relation picker.
 *
 * @method $this    orderColumn(string $val)            order by column.
 * @method $this    ajax(bool $val)                     load items with ajax.
 * @method $this    ajaxController(string $val)         controller holding widget working with ajax options.
 * @method $this    ajaxControllerAction(string $val)   controller action working with ajax options.
 * @method $this    ajaxControllerItem(string $val)     controller action parameter working with ajax options.
 */
class BelongsToPickerWidget extends RelationWidget
{
    public function init()
    {
        parent::init();
        $this->tag('belongs-to-picker-widget');
        $this->ajax(false);
        $this->indexTag("belongs-to-picker-view")->viewTag("belongs-to-picker-view");
        $this->orderColumn("id");
    }

    public function postInit()
    {
        parent::postInit();
        $this->rules('exists:'.$this->getRelatedModelTable().','.$this->getOwnerKey());
    }

    protected function store(Request $request, Model $item)
    {
        $item->{ $this->property("name") }()->associate($request->input($this->property("name")));
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $response->{ $this->property("name") } = optional($item->{ $this->property("name") })->{ $this->ownerKey };
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

    public function getOptions()
    {
        if ($this->property('ajax') && !in_array($this->controllerProperties['type'], ['index', 'show'])) {
            return [];
        }
        return parent::getOptions();
    }
}
