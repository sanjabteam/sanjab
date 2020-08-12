<?php

namespace Sanjab\Widgets\File;

use stdClass;
use Sanjab\Sanjab;
use Sanjab\Widgets\Widget;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Uppy upload widget.
 *
 * @method $this    max(integer $val)                   max number of files.
 * @method $this    min(integer $val)                   min number of files.
 * @method $this    maxSize(integer $val)               max size of file.
 * @method $this    disk(string $val)                   disk to upload.
 * @method $this    directory(string $val)              directory to save to.
 * @method $this    fileRules(string $val)              file validation rules.
 * @method $this    fileStoreCallBack(callable $val)    callback to store file. parameters(\Illuminate\Http\UploadedFile $file) and returns relative file path.
 * @method $this    width(integer $val)                 width to resize images only.
 * @method $this    height(integer $val)                height to resize images only.
 * @method $this    extension(string $val)              change all image extensions to this.
 */
class UppyWidget extends Widget
{
    /**
     * create new uppy widget just for images.
     *
     * @param  null  $name
     * @param  null  $title
     * @return static
     */
    final public static function image($name = null, $title = null)
    {
        return static::create($name, $title)->imageOnly();
    }

    /**
     * create new uppy widget just for videos.
     *
     * @param  null  $name
     * @param  null  $title
     * @return static
     */
    final public static function video($name = null, $title = null)
    {
        return static::create($name, $title)->videoOnly();
    }

    /**
     * create new uppy widget just for audios.
     *
     * @param  null  $name
     * @param  null  $title
     *
     * @return static
     */
    final public static function audio($name = null, $title = null)
    {
        return static::create($name, $title)->audioOnly();
    }

    public function init(): void
    {
        Sanjab::clearUploadCache();
        $this->directory('/');
        $this->multiple(false);
        $this->onIndex(false);
        $this->searchable(false);
        $this->tag('uppy-widget');
        $this->viewTag('uppy-view');
        $this->mimeTypes(['image/*', 'video/*', 'audio/*']);
        $this->disk('public');
        $this->min(0);
        $this->max(10);
        $this->maxSize(4096);

        $this->fileStoreCallBack(function (UploadedFile $file) {
            $extension = mb_strtolower($file->getClientOriginalExtension());
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                if ($this->property('extension')) {
                    $extension = mb_strtolower($this->property('extension'));
                }
                $image = Image::make($file)->limitColors(127);
                if ($extension == 'jpeg' || $extension == 'jpg') {
                    $image->interlace();
                }
                if ($this->property('width') && $this->property('height')) {
                    $image->fit($this->property('width'), $this->property('height'));
                } elseif ($this->property('width')) {
                    $image->widen($this->property('width'));
                } elseif ($this->property('height')) {
                    $image->heighten($this->property('height'));
                }
                if ($this->property('watermark')) {
                    $image->insert($this->property('watermark'), $this->property('watermarkPosition'), $this->property('watermarkX'), $this->property('watermarkY'));
                }
                $fileContent = $image->encode($extension);
                $filename = trim(trim($this->property('directory'), '\\/').'/'.$file->hashName(), '\\/');
                Storage::disk($this->property('disk'))->put($filename, $fileContent);

                return $filename;
            }

            return $file->storeAs($this->property('directory'), pathinfo($file->hashName(), PATHINFO_FILENAME).'.'.$extension, $this->property('disk'));
        });
    }

    protected function store(Request $request, Model $item)
    {
        $values = [];
        $name = $this->property('name');
        $oldValues = $item->$name;
        $oldValues = $this->wrap($oldValues);
        if (is_array($request->input($name))) {
            foreach ($request->input($name) as $uploadedFile) {
                if ($uploadedFile instanceof UploadedFile) {
                    $values[] = $this->property('fileStoreCallBack')($uploadedFile);
                } elseif (in_array($uploadedFile, $oldValues)) {
                    $values[] = $uploadedFile;
                }
            }
        }
        if (! $this->property('multiple')) {
            $values = array_first($values);
        }
        $item->$name = $values;
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $name = $this->property('name');
        $files = $item->$name;
        $files = $this->wrap($files);
        foreach ($files as $key => $value) {
            $value = trim($value, '\\/');
            if ($this->getDisk()->exists($value)) {
                $routeParams = ['path' => $value, 'disk' => $this->property('disk')];
                $files[$key] = [
                    'type' => $this->getDisk()->mimeType($value),
                    'preview' => route('sanjab.helpers.uppy.preview', $routeParams + ['thumb' => 'true']),
                    'link' => route('sanjab.helpers.uppy.preview', $routeParams),
                    'value' => $value,
                ];
            }
        }
        $response->$name = $files;
    }

    protected function modifyRequest(Request $request, Model $item = null)
    {
        $name = $this->property('name');
        if (! is_array($request->input($name))) {
            return null;
        }

        $oldValues = optional($item)->$name;
        $oldValues = $this->wrap($oldValues);
        foreach ($oldValues as $key => $oldValue) {
            $oldValues[$key] = trim($oldValue, '/\\');
        }
        $uploadedFiles = [];
        foreach ($request->input($name) as $fileInfo) {
            if (! is_array($fileInfo) || ! isset($fileInfo['value'])) {
                continue;
            }

            $fileInfo['value'] = preg_replace('/.*\/helpers\/uppy\/upload\//', '', $fileInfo['value']);
            $sessionInfo = session('sanjab_uppy_files.'.$fileInfo['value']);
            if (is_array($sessionInfo) && File::exists($sessionInfo['file_path'])) {
                $fileInfo = $sessionInfo;
                $uploadedFiles[] = new UploadedFile($fileInfo['file_path'], $fileInfo['name'], File::mimeType($fileInfo['file_path']), 0, true);
            } elseif (in_array($fileInfo['value'], $oldValues)) {
                $uploadedFiles[] = $fileInfo['value'];
            }
        }
        $request->merge([$name => $uploadedFiles]);
    }

    public function validationRules(Request $request, string $type, Model $item = null): array
    {
        $name = $this->property('name');
        $oldValues = optional($item)->$name;
        $oldValues = $this->wrap($oldValues);

        $fileRules = $this->property('fileRules');
        if (! is_array($fileRules)) {
            $fileRules = empty($fileRules) ? [] : explode('|', $fileRules);
        }

        $rules = [$this->name => array_merge($this->property('rules.'.$type, []), ['array', 'min:'.$this->property('min'), 'max:'.($this->property('multiple') ? $this->property('max') : 1)])];
        if (is_array($request->input($name))) {
            foreach ($request->input($name) as $key => $uploadedFile) {
                if ($uploadedFile instanceof UploadedFile) {
                    $rules[$this->name.'.'.$key] = array_merge($fileRules, ['file', 'mimetypes:'.implode(',', $this->property('mimeTypes')), 'max:'.$this->property('maxSize')]);
                }
            }
        }

        return $rules;
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

        return $this->setProperty('mimeTypes', $types);
    }

    /**
     * Allow upload only images.
     *
     * @return $this
     */
    public function imageOnly()
    {
        return $this->mimeTypes(['image/*']);
    }

    /**
     * Allow upload only videos.
     *
     * @return $this
     */
    public function videoOnly()
    {
        return $this->mimeTypes(['video/*']);
    }

    /**
     * Allow upload only audios.
     *
     * @return $this
     */
    public function audioOnly()
    {
        return $this->mimeTypes(['audio/*']);
    }

    /**
     * Allow to upload multiple files.
     *
     * @param  bool  $val
     *
     * @return $this
     */
    public function multiple($val = true)
    {
        return $this->setProperty('multiple', $val);
    }

    /**
     * Set watermark for images only.
     *
     * @param mixed $watermark      watermark image
     * @param string $position      position of watermark
     * @param int $x                watermark position x
     * @param int $y                watermark position y
     * @return $this
     */
    public function watermark($watermark, string $position = 'top-left', int $x = 0, int $y = 0)
    {
        $this->setProperty('watermark', $watermark);
        $this->setProperty('watermarkPosition', $position);
        $this->setProperty('watermarkX', $x);
        $this->setProperty('watermarkY', $y);

        return $this;
    }

    /**
     * Model event.
     *
     * @param Model $item
     * @return void
     */
    public function onUpdating(Model $item)
    {
        $name = $this->property('name');
        $oldFiles = $item->fresh()->$name;
        $newFiles = $item->$name;
        if (! is_array($oldFiles)) {
            $oldFiles = [$oldFiles];
        }
        if (! is_array($newFiles)) {
            $newFiles = [$newFiles];
        }
        foreach (array_diff($oldFiles, $newFiles) as $oldFile) {
            if ($this->getDisk()->exists($oldFile)) {
                $this->getDisk()->delete($oldFile);
            }
        }
    }

    /**
     * Model event ( not for soft delete ).
     *
     * @param Model $item
     * @return void
     */
    public function onDeleting(Model $item)
    {
        $name = $this->property('name');
        $files = $item->$name;
        if (! is_array($files)) {
            $files = [$files];
        }
        foreach ($files as $file) {
            if ($this->getDisk()->exists($file)) {
                $this->getDisk()->delete($file);
            }
        }
    }

    /**
     * Get disk instance.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function getDisk()
    {
        return Storage::disk($this->property('disk'));
    }

    /**
     * Wrap value inside array if is not array.
     *
     * @param mixed $value
     * @return array
     */
    private function wrap($value): array
    {
        if (! is_array($value)) {
            $value = empty($value) ? [] : [$value];
        }

        return $value;
    }
}
