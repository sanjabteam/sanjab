<?php

namespace Sanjab\Cards;

/**
 * Doughnut chart card.
 */
class DoughnutChartCard extends ChartCard
{
    public function init()
    {
        parent::init();
        $this->chartTag('doughnut-chart');
    }
}
