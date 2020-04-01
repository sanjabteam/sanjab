<?php

namespace Sanjab\Widgets;

class TextWidget extends Widget
{
    public function init()
    {
        $this->tag('b-form-input');
        $this->setProperty('floatlabel', true);
    }
}
