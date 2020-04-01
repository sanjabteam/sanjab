<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Sanjab\Helpers\SearchType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Select widget.
 */
class SelectWidget extends Widget
{
    protected $getters = [
        'options',
    ];

    protected $selectOptions = [];

    public function init()
    {
        $this->tag('select-widget');
        $this->indexTag('select-view')->viewTag('select-view');
    }

    public function postInit()
    {
        if ($this->property('multiple')) {
            $this->searchable(false);
        }
    }

    /**
     * Add option to options.
     *
     * @param mixed $key
     * @param string $title
     * @return $this
     */
    public function addOption($key, string $title)
    {
        $this->selectOptions[$key] = $title;

        return $this;
    }

    /**
     * Add multiple options.
     *
     * @param array $options
     * @return $this
     */
    public function addOptions(array $options)
    {
        foreach ($options as $key => $title) {
            $this->selectOptions[$key] = $title;
        }

        return $this;
    }

    /**
     * multiple select.
     *
     * @param bool $multiple
     * @return $this
     */
    public function multiple(bool $multiple = true)
    {
        $this->setProperty('multiple', $multiple);

        return $this;
    }

    protected function searchTypes(): array
    {
        return [
            SearchType::create('empty', trans('sanjab::sanjab.is_empty')),
            SearchType::create('not_empty', trans('sanjab::sanjab.is_not_empty')),
            SearchType::create('equal', trans('sanjab::sanjab.equal'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.equal'))->addOptions($this->selectOptions)),
            SearchType::create('not_equal', trans('sanjab::sanjab.not_equal'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.not_equal'))->addOptions($this->selectOptions)),
            SearchType::create('similar', trans('sanjab::sanjab.similar'))
                        ->addWidget(TextWidget::create('search', trans('sanjab::sanjab.similar'))),
            SearchType::create('not_similar', trans('sanjab::sanjab.not_similar'))
                        ->addWidget(TextWidget::create('search', trans('sanjab::sanjab.not_similar'))),
            SearchType::create('in', trans('sanjab::sanjab.is_in'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.is_in'))->addOptions($this->selectOptions)->multiple()),
            SearchType::create('not_in', trans('sanjab::sanjab.is_not_in'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.is_not_in'))->addOptions($this->selectOptions)->multiple()),
        ];
    }

    protected function search(Builder $query, string $type = null, $search = null)
    {
        $filteredOptions = [];
        if (! is_array($search)) {
            $filteredOptions = array_filter($this->selectOptions, function ($selectOption) use ($search) {
                return preg_match('/.*'.preg_quote($search).'.*/i', $selectOption);
            });
        }
        switch ($type) {
            case 'equal':
                $query->where($this->property('name'), $search);
                break;
            case 'not_equal':
                $query->where($this->property('name'), '!=', $search);
                break;
            case 'similar':
                if (count($filteredOptions) > 0) {
                    $query->whereIn($this->property('name'), array_keys($filteredOptions));
                }
                break;
            case 'not_similar':
                if (count($filteredOptions) > 0) {
                    $query->whereNotIn($this->property('name'), array_keys($filteredOptions));
                }
                break;
            case 'in':
                if (is_array($search) && count($search) > 0) {
                    $query->whereIn($this->property('name'), $search);
                }
                break;
            case 'not_in':
                if (is_array($search) && count($search) > 0) {
                    $query->whereNotIn($this->property('name'), $search);
                }
                break;
            default:
                parent::search($query, $type, $search);
                break;
        }
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
        if ($this->property('multiple')) {
            return [
                $this->name => $this->property('rules.'.$type, []),
                $this->name.'.*' => ['in:'.implode(',', array_keys($this->selectOptions))],
            ];
        } else {
            return [
                $this->name => $this->property('rules.'.$type, ['in:'.implode(',', array_keys($this->selectOptions))]),
            ];
        }
    }

    /**
     * Get select options.
     *
     * @return array
     */
    public function getOptions()
    {
        $out = [];
        foreach ($this->selectOptions as $optionKey => $optionTitle) {
            $out[] = ['label' => $optionTitle, 'value' => $optionKey];
        }

        return $out;
    }
}
