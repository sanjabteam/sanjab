<?php

namespace Sanjab\Cards;

/**
 * Scatter chart card.
 */
class ScatterChartCard extends ChartCard
{
    public function init()
    {
        parent::init();
        $this->chartTag('scatter-chart');
    }
}
