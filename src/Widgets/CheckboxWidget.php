<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Single check box widget
 *
 * @method $this fastChange (boolean $val)                          change checkbox on index.
 * @method $this fastChangeTimestamps (boolean $val)                should timestamp update on fast change.
 * @method $this fastChangeBefore(callable $callback)               callback call before fast change happen.
 * @method $this fastChangeAfter(callable $callback)                callback call after fast change happened.
 * @method $this fastChangeController(callable $callback)           controller to use with fast change.
 * @method $this fastChangeControllerAuthorize(callable $callback)  authorize fast change.
 * @method $this fastChangeControllerAction(callable $callback)     controller to use with fast change.
 * @method $this fastChangeControllerItem(callable $callback)       controller action parameter working with fast change.
 */
class CheckboxWidget extends Widget
{
    protected $getters = [
        'content',
        'controller',
        'controllerAction',
        'controllerItem',
    ];

    public function init(): void
    {
        $this->tag("b-form-checkbox");
        $this->indexTag("checkbox-view");
        $this->viewTag("checkbox-view");
        $this->fastChange(false);
        $this->fastChangeTimestamps(false);
        $this->fastChangeBefore(function (Model $item) {
        });
        $this->fastChangeAfter(function (Model $item) {
        });
        $this->fastChangeControllerAuthorize(function (Model $item) {
            $controller = $this->getController();
            return Auth::user()->can('edit'.$controller::property('permissionKey'), $item);
        });
    }

    protected function store(Request $request, Model $item)
    {
        $item->{ $this->property("name") } = $request->input($this->property("name")) == "true";
    }

    protected function search(Builder $query, string $type = null, $search = null): void
    {
        if ($search == "true") {
            $query->where($this->property('name'), 1);
        }
        if ($search == "false") {
            $query->where($this->property('name'), 0);
        }
    }

    /**
     * Title of checkbox
     *
     * @return string
     */
    public function getContent()
    {
        return $this->property('title');
    }

    public function getController()
    {
        if ($this->property('fastChange') && isset($this->controllerProperties['controller']) == false && empty($this->property('changeController'))) {
            throw new Exception("Please set fast change controller for '".$this->property('name')."'");
        }
        if (isset($this->controllerProperties['controller'])) {
            return $this->controllerProperties['controller'];
        }
        return $this->property('fastChangeController');
    }

    public function getControllerAction()
    {
        if ($this->property('fastChange') && isset($this->controllerProperties['type']) == false && empty($this->property('fastChangeControllerAction'))) {
            throw new Exception("Please set fast change controller action for '".$this->property('name')."'");
        }
        if (isset($this->controllerProperties['type'])) {
            return $this->controllerProperties['type'];
        }
        return $this->property('fastChangeControllerAction');
    }

    public function getControllerItem()
    {
        if (isset($this->controllerProperties['item'])) {
            return optional($this->controllerProperties['item'])->id;
        }
        return optional($this->property('fastChangeControllerItem'))->id;
    }
}
