<?php

namespace Sanjab\Widgets\Wysiwyg;

use Sanjab\Widgets\Widget;
use Sanjab\Sanjab;
use stdClass;
use Illuminate\Database\Eloquent\Model;

/**
 * Wysiwyg editor.
 */
class EditorJsWidget extends Widget
{
    public function init()
    {
        $this->setProperty("tag", "editor-js-widget");
        $this->setProperty("onIndex", false);
        $this->setProperty('showHtml', true);
    }

    /**
     * To modify response.
     *
     * @param object $respones
     * @param Model $item
     * @return void
     */
    protected function modifyResponse(stdClass $response, Model $item)
    {
        if (isset($this->controllerProperties['type']) && $this->controllerProperties['type'] == 'show') {
            if (is_array($item->{ $this->property("name") })) {
                $response->{ $this->property("name") } = Sanjab::editorJsToHtml($item->{ $this->property("name") });
            } else {
                $response->{ $this->property("name") } = null;
            }
        } else {
            $response->{ $this->property("name") } = $item->{ $this->property("name") };
        }
    }
}
