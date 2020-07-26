<?php

namespace Sanjab\Widgets\Relation;

use stdClass;
use Illuminate\Http\Request;
use Sanjab\Helpers\SearchType;
use Sanjab\Widgets\TextWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

/**
 * Belongs to relation picker.
 *
 * @method $this    orderColumn(string $val)            order by column.
 * @method $this    ajax(bool $val)                     load items with ajax.
 * @method $this    ajaxController(string $val)         controller holding widget working with ajax options.
 * @method $this    creatable(callable $val)            create new if does not exists.
 * @method $this    creatableText(string $val)          creatable text.
 */
class BelongsToPickerWidget extends RelationWidget
{
    public function init()
    {
        parent::init();
        $this->tag('belongs-to-picker-widget');
        $this->ajax(false);
        $this->indexTag('belongs-to-picker-view')->viewTag('belongs-to-picker-view');
        $this->orderColumn('id');
        $this->creatableText(trans('sanjab::sanjab.create'));
    }

    public function postInit()
    {
        parent::postInit();

        if ($this->property('ajax')) {
            Session::put('sanjab_relation_widget_'.$this->getController().'_'.$this->property('name'), serialize($this));
        }
    }

    protected function store(Request $request, Model $item)
    {
        if ($this->property('creatable') && is_array($request->input($this->name)) && $request->input($this->name.'.create_new') == 'true' && ! empty($this->name.'.value')) {
            $item->{ $this->property('name') }()->associate($this->property('creatable')($request->input($this->name.'.value')));
        } else {
            $item->{ $this->property('name') }()->associate($request->input($this->property('name')));
        }
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $response->{ $this->property('name') } = optional($item->{ $this->property('name') })->{ $this->ownerKey };
    }

    public function getController()
    {
        if ($this->property('ajax') && isset($this->controllerProperties['controller']) == false && empty($this->property('ajaxController'))) {
            throw new \Exception("Please set ajax controller for '".$this->property('name')."'");
        }
        if (isset($this->controllerProperties['controller'])) {
            return $this->controllerProperties['controller'];
        }

        return $this->property('ajaxController');
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

    public function validationRules(Request $request, string $type, Model $item = null): array
    {
        return [
            $this->name => array_merge(
                $this->property('rules.'.$type, []),
                $this->property('creatable') && is_array($request->input($this->name)) && $request->input($this->name.'.create_new') == 'true' && ! empty($this->name.'.value') ? [] : ['exists:'.$this->getRelatedModelTable().','.$this->getOwnerKey()]
            ),
        ];
    }

    protected function searchTypes(): array
    {
        return [
            SearchType::create('empty', trans('sanjab::sanjab.is_empty')),
            SearchType::create('not_empty', trans('sanjab::sanjab.is_not_empty')),
            SearchType::create('equal', trans('sanjab::sanjab.equal'))
                        ->addWidget($this->copy()->title(trans('sanjab::sanjab.equal'))->setProperty('searchWidget', true)),
            SearchType::create('not_equal', trans('sanjab::sanjab.not_equal'))
                        ->addWidget($this->copy()->title(trans('sanjab::sanjab.not_equal'))->setProperty('searchWidget', true)),
            SearchType::create('similar', trans('sanjab::sanjab.similar'))
                        ->addWidget(TextWidget::create('search', trans('sanjab::sanjab.similar'))),
            SearchType::create('not_similar', trans('sanjab::sanjab.not_similar'))
                        ->addWidget(TextWidget::create('search', trans('sanjab::sanjab.not_similar'))),
            SearchType::create('in', trans('sanjab::sanjab.is_in'))
                        ->addWidget($this->copy()->title(trans('sanjab::sanjab.is_in'))->setProperty('multiple', true)->setProperty('searchWidget', true)),
            SearchType::create('not_in', trans('sanjab::sanjab.is_not_in'))
                        ->addWidget($this->copy()->title(trans('sanjab::sanjab.is_not_in'))->setProperty('multiple', true)->setProperty('searchWidget', true)),
        ];
    }

    protected function search(Builder $query, string $type = null, $search = null)
    {
        switch ($type) {
            case 'empty':
                $query->whereNull($query->getQuery()->from.'.'.$this->getForeignKey());
                break;
            case 'not_empty':
                $query->whereNotNull($query->getQuery()->from.'.'.$this->getForeignKey());
                break;
            case 'equal':
                $query->where($query->getQuery()->from.'.'.$this->getForeignKey(), $search);
                break;
            case 'not_equal':
                $query->where($query->getQuery()->from.'.'.$this->getForeignKey(), '!=', $search);
                break;
            case 'not_similar':
                foreach ($this->property('searchFields') as $searchField) {
                    $relation = preg_replace('/\.[A-Za-z0-9_]+$/', '', $this->property('name').'.'.$searchField);
                    $field = str_replace($relation.'.', '', $this->property('name').'.'.$searchField);
                    $query->orWhereHas($relation, function (Builder $query) use ($field, $search) {
                        $query->where($query->getQuery()->from.'.'.$field, 'NOT LIKE', '%'.$search.'%');
                    });
                }
                break;
            case 'in':
                if (is_array($search) && count($search) > 0) {
                    $query->whereIn($query->getQuery()->from.'.'.$this->getForeignKey(), $search);
                }
                break;
            case 'not_in':
                if (is_array($search) && count($search) > 0) {
                    $query->whereNotIn($query->getQuery()->from.'.'.$this->getForeignKey(), $search);
                }
                break;
            default:
                parent::search($query, $type, $search);
                break;
        }
    }
}
