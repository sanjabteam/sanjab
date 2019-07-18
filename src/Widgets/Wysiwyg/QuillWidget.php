<?php

namespace Sanjab\Widgets\Wysiwyg;

use Sanjab\Widgets\Widget;

/**
 * Wysiwyg editor.
 */
class QuillWidget extends Widget
{
    public function init()
    {
        $this->setProperty("tag", "quill-widget");
        $this->setProperty("rows", 4);
        $this->setProperty("onIndex", false);
        $this->setProperty('showHtml', true);
    }
}
