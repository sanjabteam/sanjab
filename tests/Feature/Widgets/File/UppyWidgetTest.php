<?php

namespace Sajab\Tests\Feature\Widgets\File;

use Illuminate\Support\Facades\File;
use Sanjab\Sanjab;
use Sanjab\Tests\TestCase;

class UppyWidgetTest extends TestCase
{
    public function testClearCache()
    {
        if (! File::isDirectory(storage_path('app/temp/test2'))) {
            File::makeDirectory(storage_path('app/temp/test2'), 0755, true);
        }
        File::put(storage_path('app/temp/test.txt'), 'Hello Test');
        File::put(storage_path('app/temp/test2/test.txt'), 'Hello Test');
        touch(storage_path('app/temp/test.txt'), time() - 90000);
        touch(storage_path('app/temp/test2/test.txt'), time() - 90000);

        Sanjab::clearUploadCache();

        $this->assertFalse(File::isDirectory(storage_path('app/temp/test2')));
        $this->assertFalse(File::exists(storage_path('app/temp/test.txt')));
    }
}
