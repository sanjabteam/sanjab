<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Single check box widget
 *
 * @method $this    changable(boolean $val)     can change checkbox on index
 */
class CheckboxWidget extends Widget
{
    protected $getters = [
        'content'
    ];

    public function init(): void
    {
        $this->tag("b-form-checkbox");
        $this->indexTag("checkbox-view");
        $this->changable(false);
    }

    protected function store(Request $request, Model $item)
    {
        $item->{ $this->property("name") } = $request->input($this->property("name")) == "true";
    }

    protected function search(Builder $query, string $search, string $type = null): void
    {
        if ($search == "true") {
            $query->where($this->property('name'), 1);
        }
        if ($search == "false") {
            $query->where($this->property('name'), 0);
        }
    }

    /**
     * Title of checkbox
     *
     * @return string
     */
    public function getContent()
    {
        return $this->property('title');
    }
}
