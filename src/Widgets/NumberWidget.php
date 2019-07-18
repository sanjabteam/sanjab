<?php

namespace Sanjab\Widgets;

class NumberWidget extends TextWidget
{
    public function init()
    {
        parent::init();
        $this->setProperty("type", "number");
        $this->rules('numeric');
    }
}
