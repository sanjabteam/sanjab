<?php

namespace Sanjab\Widgets;

class ColorWidget extends Widget
{
    public function init()
    {
        $this->tag("b-form-input");
        $this->type("color");
        $this->rules('regex:/#[a-fA-F0-9]{6}/');
        $this->viewTag('color-view');
    }
}
