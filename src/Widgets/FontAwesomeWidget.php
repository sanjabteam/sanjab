<?php

namespace Sanjab\Widgets;

use Sanjab\Sanjab;
use Sanjab\Helpers\SearchType;

/**
 * Font Awesome picker.
 */
class FontAwesomeWidget extends SelectWidget
{
    public function init()
    {
        parent::init();
        $this->tag('font-awesome-widget');
        $this->indexTag('font-awesome-view')->viewTag('font-awesome-view');
        $this->addOptions(Sanjab::fontawesomeIcons());
    }

    public function postInit()
    {
        $this->rules('in:'.implode(',', array_keys($this->selectOptions)));
    }

    protected function searchTypes(): array
    {
        return [
            SearchType::create('empty', trans('sanjab::sanjab.is_empty')),
            SearchType::create('not_empty', trans('sanjab::sanjab.is_not_empty')),
            SearchType::create('equal', trans('sanjab::sanjab.equal'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.equal'))),
            SearchType::create('not_equal', trans('sanjab::sanjab.not_equal'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.not_equal'))),
            SearchType::create('in', trans('sanjab::sanjab.is_in'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.is_in'))->multiple()),
            SearchType::create('not_in', trans('sanjab::sanjab.is_not_in'))
                        ->addWidget(self::create('search', trans('sanjab::sanjab.is_not_in'))->multiple()),
        ];
    }
}
