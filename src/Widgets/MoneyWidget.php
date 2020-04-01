<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * @method $this decimal(string $val)       decimal seperator
 * @method $this thousands(string $val)     thousands seperator
 * @method $this prefix(string $val)        money prefix
 * @method $this postfix(string $val)       money suffix
 * @method $this precision(int $val)        money precision
 * @method $this zeroAsNull(bool $val)      set zero as null in model
 */
class MoneyWidget extends NumberWidget
{
    public function init()
    {
        parent::init();
        $this->setProperty('type', 'text');
        $this->tag('money-widget');
        $this->viewTag('money-view');
        $this->indexTag('money-view');
        $this->decimal(',');
        $this->postfix(' $');
        $this->zeroAsNull(false);
    }

    /**
     * To override modifying request.
     *
     * @param Request $request
     * @param null|Model $item
     * @return void
     */
    protected function modifyRequest(Request $request, Model $item = null)
    {
        if ($this->property('zeroAsNull') && $request->input($this->property('name')) == '0') {
            $request->merge([$this->property('name') => null]);
        }
    }
}
