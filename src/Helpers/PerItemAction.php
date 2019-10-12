<?php

namespace Sanjab\Helpers;

class PerItemAction extends Action
{
    public function __construct()
    {
        parent::__construct();
        $this->perItem(true);
    }
}
