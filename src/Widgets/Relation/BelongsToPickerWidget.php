<?php

namespace Sanjab\Widgets\Relation;

use stdClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Belongs to relation picker.
 *
 * @method $this    orderColumn(string $val)    order by column.
 */
class BelongsToPickerWidget extends RelationWidget
{
    public function init()
    {
        parent::init();
        $this->tag('select-widget');
        $this->indexTag("select-view")->viewTag("select-view");
        $this->orderColumn("id");
    }

    public function postInit()
    {
        parent::postInit();
        $this->rules('exists:'.$this->getRelatedModelTable().','.$this->getOwnerKey());
    }

    protected function store(Request $request, Model $item)
    {
        $item->{ $this->property("name") }()->associate($request->input($this->property("name")));
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $response->{ $this->property("name") } = optional($item->{ $this->property("name") })->{ $this->ownerKey };
    }
}
