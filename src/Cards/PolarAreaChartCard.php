<?php

namespace Sanjab\Cards;

/**
 * Polar chart card.
 */
class PolarAreaChartCard extends ChartCard
{
    public function init()
    {
        parent::init();
        $this->chartTag('polar-area-chart');
    }
}
