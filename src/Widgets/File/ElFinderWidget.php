<?php

namespace Sanjab\Widgets\File;

use stdClass;
use Sanjab\Widgets\Widget;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Uppy upload widget.
 *
 * @method $this    max(integer $val)               max number of files.
 * @method $this    min(integer $val)               min number of files.
 * @method $this    maxSize(integer $val)           max size of file.
 * @method $this    disk(string $val)               default disk.
 * @method $this    fileRules(string $val)          file validation rules.
 * @method $this    width(integer $val)             width to resize images only.
 * @method $this    height(integer $val)            height to resize images only.
 */
class ElFinderWidget extends Widget
{
    /**
     * create new uppy widget just for images.
     *
     * @return static
     */
    final public static function image($name = null, $title = null)
    {
        return static::create($name, $title)->imageOnly();
    }

    /**
     * create new uppy widget just for videos.
     *
     * @return static
     */
    final public static function video($name = null, $title = null)
    {
        return static::create($name, $title)->videoOnly();
    }

    /**
     * create new uppy widget just for videos.
     *
     * @return static
     */
    final public static function audio($name = null, $title = null)
    {
        return static::create($name, $title)->audioOnly();
    }

    public function init(): void
    {
        $this->multiple(false);
        $this->onIndex(false);
        $this->searchable(false);
        $this->tag("elfinder-widget");
        $this->viewTag("uppy-view");
        $this->mimeTypes(["image/*", "video/*", "audio/*"]);
        $this->disk('public');
        $this->min(0);
        $this->max(10);
        $this->maxSize(4096);
    }

    protected function store(Request $request, Model $item)
    {
        if ($this->property("multiple")) {
            $item->{ $this->property('name') } = $request->input($this->property('name'));
        } else {
            $item->{ $this->property('name') } = array_first($request->input($this->property('name')));
        }
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $files = $item->{ $this->property('name') };
        if (! is_array($files)) {
            if (empty($files)) {
                $files = [];
            } else {
                $files = [$files];
            }
        }
        foreach ($files as $key => $value) {
            $value = trim($value, '\\/');
            $files[$key] = [
                'type' => Storage::disk($this->property('disk'))->mimeType($value),
                'preview' => route('sanjab.helpers.uppy.preview', ['path' => $value, 'disk' => $this->property('disk'), 'thumb' => 'true']),
                'link' => route('sanjab.helpers.uppy.preview', ['path' => $value, 'disk' => $this->property('disk')]),
                'value' => $value
            ];
        }
        $response->{ $this->property('name') } = $files;
    }

    protected function modifyRequest(Request $request, Model $item = null)
    {
        if (is_array($request->input($this->property('name')))) {
            $files = [];
            $fakeUploadedFiles = [];
            foreach ($request->input($this->property('name')) as $fileInfo) {
                if (is_array($fileInfo) && isset($fileInfo['value']) && Storage::disk($this->property('disk'))->exists($fileInfo['value'])) {
                    $files[] = $fileInfo['value'];
                    $fakeUploadedFiles[] = new UploadedFile(Storage::disk($this->property('disk'))->path($fileInfo['value']), $fileInfo['value'], Storage::disk($this->property('disk'))->mimeType($fileInfo['value']), 0, true);
                }
            }
            $request->merge([
                $this->property('name') => $files,
                $this->property('name').'_fake' => $fakeUploadedFiles
            ]);
        }
    }

    public function validationRules(Request $request, string $type, Model $item = null): array
    {
        $fileRules = $this->property('fileRules');
        if (! is_array($fileRules)) {
            if (empty($fileRules)) {
                $fileRules = [];
            } else {
                $fileRules = explode('|', $fileRules);
            }
        }

        $rules = [$this->name => array_merge($this->property('rules.'.$type, []), ['array', 'min:'.$this->property('min'), 'max:'.($this->property('multiple') ? $this->property('max') : 1)])];
        $rules[$this->name.'_fake.*'] = array_merge($fileRules, ['file', 'mimetypes:'.implode(',', $this->property('mimeTypes')), 'max:'.$this->property('maxSize')]);
        return $rules;
    }

    public function validationAttributes(Request $request, string $type, Model $item = null): array
    {
        return array_merge(parent::validationAttributes($request, $type, $item), [$this->name.'_fake.*' => $this->property('title')]);
    }

    /**
     * Set allowed types.
     *
     * @return $this
     */
    public function mimeTypes($types)
    {
        if (! is_array($types)) {
            $types = [$types];
        }
        $this->setProperty('mimeTypes', $types);
        return $this;
    }

    /**
     * Allow upload only images.
     *
     * @return $this
     */
    public function imageOnly()
    {
        $this->mimeTypes(['image/*']);
        return $this;
    }

    /**
     * Allow upload only videos.
     *
     * @return $this
     */
    public function videoOnly()
    {
        $this->mimeTypes(['video/*']);
        return $this;
    }

    /**
     * Allow upload only audios.
     *
     * @return $this
     */
    public function audioOnly()
    {
        $this->mimeTypes(['audio/*']);
        return $this;
    }

    /**
     * Allow to upload multiple files.
     *
     * @property bool $val
     * @return $this
     */
    public function multiple($val = true)
    {
        $this->setProperty('multiple', $val);
        return $this;
    }
}
