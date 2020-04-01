<?php

namespace Sanjab\Cards;

use stdClass;

/**
 * Simple statistics card.
 *
 * @method $this footerTitle(string $val)       title in footer.
 * @method $this footerIcon(string $val)        icon in footer.
 * @method $this link(string $val)              link of card.
 * @method $this value(callable|string $val)    value of stats.
 * @method $this icon(string $val)              icon of stats.
 * @method $this variant(string $val)           variant of stat card.
 */
class StatsCard extends Card
{
    public function init()
    {
        $this->tag('simple-stats-card');
        $this->cols(3);
    }

    protected function modifyResponse(stdClass $response)
    {
        if (is_callable($this->property('value'))) {
            $response->data = $this->property('value')();
        } else {
            $response->data = $this->property('value');
        }
    }
}
