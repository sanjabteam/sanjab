<?php

namespace Sanjab\Widgets;

use stdClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class PasswordWidget extends Widget
{
    public function init()
    {
        $this->tag("b-form-input")
            ->onIndex(false)
            ->onView(false)
            ->searchable(false)
            ->sortable(false);
        $this->setProperty("floatlabel", true);
        $this->setProperty("type", "password");
    }

    public function store(Request $request, Model $item)
    {
        $item->{ $this->property("name") } = bcrypt($request->input($this->property("name")));
    }

    protected function modifyResponse(stdClass $response, Model $item): void
    {
    }
}
