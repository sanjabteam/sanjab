<?php

namespace Sanjab\Widgets\Relation;

use stdClass;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Sanjab\Helpers\SearchType;
use Sanjab\Widgets\TextWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

/**
 * Belongs to many select box.
 *
 * @method $this    max(integer $val)                   maximum count of related.
 * @method $this    ajax(bool $val)                     should this work with ajax.
 * @method $this    ajaxController(string $val)         controller holding widget working with ajax options.
 * @method $this    pivotValues(array $val)             pivot values.
 * @method $this    creatable(callable $val)            create new if does not exists.
 * @method $this    creatableText(string $val)          creatable text.
 */
class BelongsToManyPickerWidget extends RelationWidget
{
    public function init()
    {
        parent::init();
        $this->onIndex(false);
        $this->sortable(false);
        $this->ajax(false);
        $this->tag('belongs-to-picker-widget');
        $this->viewTag('belongs-to-picker-view');
        $this->setProperty('multiple', true);
        $this->setProperty('tagging', true);
        $this->creatableText(trans('sanjab::sanjab.create'));
    }

    public function postInit()
    {
        parent::postInit();

        if ($this->property('ajax')) {
            Session::put('sanjab_relation_widget_'.$this->getController().'_'.$this->property('name'), serialize($this));
        }
    }

    public function getController()
    {
        $name = $this->property('name');
        if ($this->property('ajax') && isset($this->controllerProperties['controller']) == false && empty($this->property('ajaxController'))) {
            throw new Exception("Please set ajax controller for '".$name."'");
        }
        if (isset($this->controllerProperties['controller'])) {
            return $this->controllerProperties['controller'];
        }

        return $this->property('ajaxController');
    }

    protected function postStore(Request $request, Model $item)
    {
        $name = $this->property('name');
        if (is_array($request->input($name))) {
            $values = $request->input($name);
            if ($this->property('creatable')) {
                foreach ($values as $key => $value) {
                    if (is_array($value) && Arr::get($value, 'create_new') == 'true' && ! empty(Arr::get($value, 'value'))) {
                        $values[$key] = $this->property('creatable')(Arr::get($value, 'value'));
                        if ($values[$key] instanceof Model) {
                            $values[$key] = $values[$key]->{ $this->relatedKey };
                        }
                    }
                }
            }
            if (is_array($this->property('pivotValues'))) {
                $valuesWithPivot = [];
                foreach ($values as $key => $value) {
                    $valuesWithPivot[$value] = $this->property('pivotValues');
                }
                $values = $valuesWithPivot;
            }
            $item->$name()->sync($values);
        }
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $name = $this->property('name');
        $type = $this->controllerProperties['type'];
        if (! in_array($type, ['index', 'show']) ||
            ($type == 'index' && $this->property('onIndex')) ||
            ($type == 'show' && $this->property('onView'))
        ) {
            $response->$name = $item->$name()->get()->pluck($this->relatedKey)->toArray();
        }
    }

    public function validationRules(Request $request, string $type, Model $item = null): array
    {
        $name = $this->property('name');
        $arrayRule = ['array'];
        if ($this->property('max')) {
            $arrayRule[] = 'max:'.$this->property('max');
        }
        if ($this->property('creatable')) {
            if (is_array($request->input($name))) {
                $rules = [$name => array_merge($arrayRule, $this->property('rules.'.$type, []))];
                foreach ($request->input($name) as $key => $value) {
                    if (! is_array($value) || Arr::get($value, 'create_new') != 'true' || empty(Arr::get($value, 'value'))) {
                        $rules[$key] = ['numeric', 'exists:'.$this->relatedModelTable.','.$this->relatedKey];
                    }
                }

                return $rules;
            }
        }

        return [
            $name       => array_merge($arrayRule, $this->property('rules.'.$type, [])),
            $name.'.*'  => ['numeric', 'exists:'.$this->relatedModelTable.','.$this->relatedKey],
        ];
    }

    public function getOptions()
    {
        if ($this->property('ajax') &&
            (! in_array($this->controllerProperties['type'], ['index', 'show']) || $this->property('searchWidget') == true)
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
        $name = $this->property('name');
        switch ($type) {
            case 'empty':
                $query->whereDoesntHave($name);
                break;
            case 'not_empty':
                $query->whereHas($name);
                break;
            case 'not_similar':
                foreach ($this->property('searchFields') as $searchField) {
                    $relation = preg_replace('/\.[A-Za-z0-9_]+$/', '', $name.'.'.$searchField);
                    $field = str_replace($relation.'.', '', $name.'.'.$searchField);
                    $query->orWhereHas($relation, function (Builder $query) use ($field, $search) {
                        $query->where($query->getQuery()->from.'.'.$field, 'NOT LIKE', '%'.$search.'%');
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
