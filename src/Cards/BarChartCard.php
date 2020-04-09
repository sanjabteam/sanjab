<?php

namespace Sanjab\Cards;

/**
 * Bar chart card.
 */
class BarChartCard extends ChartCard
{
    public function init()
    {
        parent::init();
        $this->chartTag('bar-chart');
    }
}
