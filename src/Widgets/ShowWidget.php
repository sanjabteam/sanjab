<?php

namespace Sanjab\Widgets;

/**
 * Show text widget.
 */
class ShowWidget extends Widget
{
    public function init()
    {
        $this->onCreate(false)
            ->onEdit(false)
            ->searchable(false)
            ->sortable(false)
            ->onStore(false);
    }
}
