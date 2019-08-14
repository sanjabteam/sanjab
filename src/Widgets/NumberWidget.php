<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * @method $this min(int $val)      minimum value.
 * @method $this max(int $val)      maximum value.
 */
class NumberWidget extends TextWidget
{
    public function init()
    {
        parent::init();
        $this->setProperty("type", "number");
        $this->min(0);
        $this->max(PHP_INT_MAX);
    }

    /**
     * Returns validation rules.
     *
     * @param Request $request
     * @property string $type 'create' or 'edit'.
     * @property Model|null $item
     * @return array
     */
    public function validationRules(Request $request, string $type, Model $item = null): array
    {
        return [
            $this->name => array_merge($this->property('rules.'.$type, []), ['numeric', 'min:'.$this->property('min'), 'max:'.$this->property('max')]),
        ];
    }
}
