<?php

namespace Sanjab\Widgets;

/**
 * @method $this decimal(string $val)       decimal seperator
 * @method $this thousands(string $val)     thousands seperator
 * @method $this prefix(string $val)        money prefix
 * @method $this postfix(string $val)       money suffix
 * @method $this precision(int $val)        money precision
 */
class MoneyWidget extends NumberWidget
{
    public function init()
    {
        parent::init();
        $this->setProperty('type', 'text');
        $this->tag("money-widget");
        $this->viewTag('money-view');
        $this->indexTag('money-view');
        $this->decimal(',');
        $this->postfix(' $');
    }
}
