<?php

namespace Sanjab\Widgets\Relation;

use stdClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Sanjab\Helpers\SearchType;
use Sanjab\Widgets\TextWidget;

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
        $this->viewTag("belongs-to-picker-view");
        $this->setProperty("multiple", true);
        $this->setProperty("tagging", true);
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

    public function validationRules(Request $request, string $type, Model $item = null): array
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
        if ($this->property('ajax') &&
            (!in_array($this->controllerProperties['type'], ['index', 'show']) || $this->property('searchWidget') == true)
        ) {
            return [];
        }
        return parent::getOptions();
    }

    protected function searchTypes(): array
    {
        return [
            SearchType::create('empty', trans('sanjab::sanjab.is_empty')),
            SearchType::create('not_empty', trans('sanjab::sanjab.is_not_empty')),
            SearchType::create('similar', trans('sanjab::sanjab.similar'))
                        ->addWidget(TextWidget::create('search', trans('sanjab::sanjab.similar'))),
            SearchType::create('not_similar', trans('sanjab::sanjab.not_similar'))
                        ->addWidget(TextWidget::create('search', trans('sanjab::sanjab.not_similar'))),
            SearchType::create('in', trans('sanjab::sanjab.is_in'))
                        ->addWidget($this->copy()->title(trans('sanjab::sanjab.is_in'))->setProperty('searchWidget', true)),
            SearchType::create('not_in', trans('sanjab::sanjab.is_not_in'))
                        ->addWidget($this->copy()->title(trans('sanjab::sanjab.is_not_in'))->setProperty('searchWidget', true)),
        ];
    }

    protected function search(Builder $query, string $type = null, $search = null)
    {
        switch ($type) {
            case 'empty':
                $query->whereHas($this->property('name'));
                break;
            case 'not_empty':
                $query->whereDoesntHave($this->property('name'));
                break;
            case 'not_similar':
                foreach ($this->property('searchFields') as $searchField) {
                    $relation = preg_replace('/\.[A-Za-z0-9_]+$/', '', $this->property("name").'.'.$searchField);
                    $field = str_replace($relation.'.', '', $this->property("name").'.'.$searchField);
                    $query->orWhereHas($relation, function (Builder $query) use ($field, $search) {
                        $query->where($query->getQuery()->from.'.'.$field, "NOT LIKE", "%".$search."%");
                    });
                }
                break;
            case 'in':
                if (is_array($search) && count($search) > 0) {
                    $query->whereHas($this->name, function (Builder $query) use ($search) {
                        $query->whereIn($query->getQuery()->from.'.'.$this->getRelatedKey(), $search);
                    });
                }
                break;
            case 'not_in':
                if (is_array($search) && count($search) > 0) {
                    $query->whereHas($this->name, function (Builder $query) use ($search) {
                        $query->whereNotIn($query->getQuery()->from.'.'.$this->getRelatedKey(), $search);
                    });
                }
                break;
            default:
                parent::search($query, $type, $search);
                break;
        }
    }
}
