<?php

namespace Sanjab\Cards;

/**
 * Bubble chart card.
 */
class BubbleChartCard extends ChartCard
{
    public function init()
    {
        parent::init();
        $this->chartTag('bubble-chart');
    }
}
