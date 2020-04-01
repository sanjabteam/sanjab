<?php

namespace Sanjab\Widgets;

/**
 * Seperator.
 */
class SeperatorWidget extends Widget
{
    public function init()
    {
        $this->groupTag('seperator-view')
            ->viewGroupTag('seperator-view')
            ->onIndex(false)
            ->searchable(false)
            ->sortable(false)
            ->onStore(false);
    }
}
