<?php

namespace Sanjab\Widgets\File;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use stdClass;
use Illuminate\Database\Eloquent\Model;

/**
 * Dropzone image upload just one image
 */
class Dropzone1Widget extends DropzoneWidget
{
    public function init()
    {
        parent::init();
        $this->max(1);
    }

    protected function store(Request $request, Model $item): void
    {
        $item->{ $this->property("name") } = [$item->{ $this->property("name") }];
        parent::store($request, $item);
        if (is_array($item->{$this->property("name")})) {
            $item->{$this->property("name")} = array_first($item->{$this->property("name")});
        } else {
            $item->{$this->property("name")} = null;
        }
    }

    public function modifyResponse(stdClass $response, Model $item)
    {
        $item->{ $this->property("name") } = [$item->{ $this->property("name") }];
        return parent::modifyResponse($response, $item);
    }

    public function onDeleting(Model $item)
    {
        $item->{ $this->property("name") } = [$item->{ $this->property("name") }];
        parent::onDeleting($item);
        $item->{$this->property("name")} = array_first($item->{$this->property("name")});
    }

    public function onUpdating(Model $item)
    {
        $oldFiles = [$item->getOriginal()[$this->property("name")]];
        $newFiles = [$item->{ $this->property("name") }];
        if (is_array($oldFiles)) {
            foreach ($oldFiles as $oldFile) {
                if (! is_array($oldFile)) {
                    $oldFile = [$oldFile];
                }
                foreach ($newFiles as $newFile) {
                    if (! is_array($newFile)) {
                        $newFile = [$newFile];
                    }
                    if (count(array_intersect($oldFile, $newFile)) > 0) {
                        continue 2;
                    }
                }
                foreach ($oldFile as $oFile) {
                    if (Storage::disk($this->property("disk"))->exists($oFile)) {
                        Storage::disk($this->property("disk"))->delete($oFile);
                    }
                }
            }
        }
    }
}
