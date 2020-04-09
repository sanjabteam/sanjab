<?php

namespace Sanjab\Cards;

/**
 * Line chart card.
 */
class LineChartCard extends ChartCard
{
    public function init()
    {
        parent::init();
        $this->chartTag('line-chart');
    }
}
