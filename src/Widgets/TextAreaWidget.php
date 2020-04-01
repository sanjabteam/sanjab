<?php

namespace Sanjab\Widgets;

/**
 * @method $this    rows(integer $val)       textarea rows.
 */
class TextAreaWidget extends Widget
{
    public function init()
    {
        $this->setProperty('floatlabel', true);
        $this->tag('b-form-textarea')
            ->rows(3)
            ->onIndex(false);
    }
}
