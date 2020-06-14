<?php

namespace Sanjab\Widgets;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Widget to show item id.
 */
class IdWidget extends NumberWidget
{
    public function postInit()
    {
        $this->name('id');
        $this->title(trans('sanjab::sanjab.id'));
        $this->onCreate(false)->onEdit(false);
    }

    protected function store(Request $request, Model $item): void
    {
    }
}
