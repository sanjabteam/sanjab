<?php

namespace Sajab\Tests\Feature\Widgets;

use Sanjab\Sanjab;
use Sanjab\Tests\TestCase;

class FontAwesomeWidgetTest extends TestCase
{
    public function testIconsList()
    {
        $icons = Sanjab::fontawesomeIcons();
        $this->assertTrue(isset($icons['fa fa-ad']));
        $icons = Sanjab::fontawesomeIcons();
        $this->assertTrue(isset($icons['fa fa-ad']));
    }
}
