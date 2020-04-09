<?php

namespace Sanjab\Cards;

/**
 * Radar chart card.
 */
class RadarChartCard extends ChartCard
{
    public function init()
    {
        parent::init();
        $this->chartTag('radar-chart');
    }
}
