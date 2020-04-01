<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Sanjab\Helpers\SearchType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method $this min(number $val)      minimum value.
 * @method $this max(number $val)      maximum value.
 * @method $this step(number $val)     step value.
 */
class NumberWidget extends TextWidget
{
    public function init()
    {
        parent::init();
        $this->setProperty('type', 'number');
        $this->min(0);
        $this->max(PHP_INT_MAX);
    }

    /**
     * Returns validation rules.
     *
     * @param Request $request
     * @property string $type 'create' or 'edit'.
     * @property Model|null $item
     * @return array
     */
    public function validationRules(Request $request, string $type, Model $item = null): array
    {
        return [
            $this->name => array_merge($this->property('rules.'.$type, []), ['numeric', 'min:'.$this->property('min'), 'max:'.$this->property('max')]),
        ];
    }

    /**
     * Get search types.
     *
     * @return array|SearchType[]
     */
    protected function searchTypes(): array
    {
        return [
            SearchType::create('empty', trans('sanjab::sanjab.is_empty')),
            SearchType::create('not_empty', trans('sanjab::sanjab.is_not_empty')),
            SearchType::create('equal', trans('sanjab::sanjab.equal'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.equal'))),
            SearchType::create('not_equal', trans('sanjab::sanjab.not_equal'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.not_equal'))),
            SearchType::create('more', trans('sanjab::sanjab.more'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.more'))),
            SearchType::create('more_or_eqaul', trans('sanjab::sanjab.more_or_eqaul'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.more_or_eqaul'))),
            SearchType::create('less', trans('sanjab::sanjab.less'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.less'))),
            SearchType::create('less_or_eqaul', trans('sanjab::sanjab.less_or_eqaul'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.less_or_eqaul'))),
            SearchType::create('between', trans('sanjab::sanjab.between'))
                        ->addWidget(self::create('first', trans('sanjab::sanjab.between')))
                        ->addWidget(self::create('second', trans('sanjab::sanjab.between'))),
            SearchType::create('not_between', trans('sanjab::sanjab.not_between'))
                        ->addWidget(self::create('first', trans('sanjab::sanjab.not_between')))
                        ->addWidget(self::create('second', trans('sanjab::sanjab.not_between'))),
            SearchType::create('even', trans('sanjab::sanjab.even')),
            SearchType::create('odd', trans('sanjab::sanjab.odd')),
            SearchType::create('divisible', trans('sanjab::sanjab.divisible'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.divisible'))),
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
            case 'more':
                $query->where($this->property('name'), '>', $search);
                break;
            case 'more_or_eqaul':
                $query->where($this->property('name'), '>=', $search);
                break;
            case 'less':
                $query->where($this->property('name'), '<', $search);
                break;
            case 'less_or_eqaul':
                $query->where($this->property('name'), '<=', $search);
                break;
            case 'between':
                $query->whereBetween($this->property('name'), [min(intval($search['first']), intval($search['second'])), max(intval($search['first']), intval($search['second']))]);
                break;
            case 'not_between':
                $query->whereNotBetween($this->property('name'), [min(intval($search['first']), intval($search['second'])), max(intval($search['first']), intval($search['second']))]);
                break;
            case 'even':
                $query->whereRaw('MOD('.$this->property('name').', 2) = 0');
                break;
            case 'odd':
                $query->whereRaw('MOD('.$this->property('name').', 2) = 1');
                break;
            case 'divisible':
                $query->whereRaw('MOD('.$this->property('name').', '.intval($search).') = 0');
                break;
            default:
                parent::search($query, $type, $search);
                break;
        }
    }
}
