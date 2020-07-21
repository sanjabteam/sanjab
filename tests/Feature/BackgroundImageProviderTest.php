<?php

namespace Sanjab\Tests\Feature;

use Sanjab\Sanjab;
use Sanjab\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class BackgroundImageProviderTest extends TestCase
{
    public function testImages()
    {
        Cache::forget('sanjab_background_details');
        $image = Sanjab::image();
        $this->assertIsArray($image);
        $this->assertTrue(isset($image['author']));
        $this->assertTrue(isset($image['link']));
        $this->assertTrue(isset($image['image']));

        // Test again after cache
        $image = Sanjab::image();
        $this->assertIsArray($image);
        $this->assertTrue(isset($image['author']));
        $this->assertTrue(isset($image['link']));
        $this->assertTrue(isset($image['image']));
    }
}
