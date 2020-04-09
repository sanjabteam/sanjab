<?php

namespace Sanjab\Cards;

/**
 * Horizontal bar chart card.
 */
class HorizontalBarChartCard extends ChartCard
{
    public function init()
    {
        parent::init();
        $this->chartTag('horizontal-bar-chart');
    }
}
