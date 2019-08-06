<?php

namespace Sanjab\Widgets;

use Sanjab\Sanjab;

/**
 * Font Awesome picker.
 */
class FontAwesomeWidget extends SelectWidget
{
    public function init()
    {
        parent::init();
        $this->tag("font-awesome-widget");
        $this->indexTag("font-awesome-view")->viewTag('font-awesome-view');
        $this->addOptions(Sanjab::fontawesomeIcons());
    }

    public function postInit()
    {
        $this->rules('in:'.implode(",", array_keys($this->selectOptions)));
    }
}
