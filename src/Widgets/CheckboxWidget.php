<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Sanjab\Helpers\SearchType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Single check box widget.
 *
 * @method $this fastChange (boolean $val)                          change checkbox on index.
 * @method $this fastChangeTimestamps (boolean $val)                should timestamp update on fast change.
 * @method $this fastChangeBefore(callable $callback)               callback call before fast change happen. parameters(Model $item)
 * @method $this fastChangeAfter(callable $callback)                callback call after fast change happened. parameters(Model $item)
 * @method $this fastChangeController(string $val)                  controller to use with fast change.
 * @method $this fastChangeControllerAuthorize(callable $callback)  authorize fast change. parameters(Model $item)
 * @method $this fastChangeControllerAction(string $val)            controller to use with fast change.
 * @method $this fastChangeControllerItem(string $val)              controller action parameter working with fast change.
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
        $this->tag('b-form-checkbox');
        $this->indexTag('checkbox-view');
        $this->viewTag('checkbox-view');
        $this->fastChange(false);
        $this->fastChangeTimestamps(false);
        $this->hideGroupLabel(true);
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
        $item->{ $this->property('name') } = $request->input($this->property('name')) == 'true';
    }

    /**
     * Title of checkbox.
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

    /**
     * Get search types.
     *
     * @return array|SearchType[]
     */
    protected function searchTypes(): array
    {
        return [
            SearchType::create('checked', '✅'),
            SearchType::create('unchecked', '❌'),
        ];
    }

    /**
     * To override search query modify.
     *
     * @param Builder $query
     * @param string $type
     * @param mixed $search
     * @return void
     */
    protected function search(Builder $query, string $type = null, $search = null)
    {
        switch ($type) {
            case 'checked':
                $query->where($this->property('name'), true);
                break;
            case 'unchecked':
                $query->where($this->property('name'), false);
                break;
        }
    }
}
