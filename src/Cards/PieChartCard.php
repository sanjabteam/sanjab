<?php

namespace Sanjab\Cards;

/**
 * Pie chart card.
 */
class PieChartCard extends ChartCard
{
    public function init()
    {
        parent::init();
        $this->chartTag('pie-chart');
    }
}
