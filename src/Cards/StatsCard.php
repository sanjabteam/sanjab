<?php

namespace Sanjab\Cards;

use stdClass;

/**
 * Simple statistics card.
 *
 * @method $this footerTitle(string $val)       title in footer.
 * @method $this footerIcon(string $val)        icon in footer.
 * @method $this link(string $val)              link of card.
 * @method $this value(callable $val)           value of stats.
 * @method $this icon(string $val)              icon of stats.
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
        if (is_callable("value")) {
            $response->data = $this->property("value")();
        } else {
            $response->data = "undefined";
        }
    }
}
