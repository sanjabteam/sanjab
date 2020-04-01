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
use Illuminate\Support\Facades\Session;
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
            if (in_array(mb_strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif'])) {
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
        $oldValues = $item->{ $this->property('name') };
        if (! is_array($oldValues)) {
            if (empty($oldValues)) {
                $oldValues = [];
            } else {
                $oldValues = [$oldValues];
            }
        }
        if (is_array($request->input($this->property('name')))) {
            foreach ($request->input($this->property('name')) as $uploadedFile) {
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
        $item->{ $this->property('name') } = $values;
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
            if (Storage::disk($this->property('disk'))->exists($value)) {
                $files[$key] = [
                    'type' => Storage::disk($this->property('disk'))->mimeType($value),
                    'preview' => route('sanjab.helpers.uppy.preview', ['path' => $value, 'disk' => $this->property('disk'), 'thumb' => 'true']),
                    'link' => route('sanjab.helpers.uppy.preview', ['path' => $value, 'disk' => $this->property('disk')]),
                    'value' => $value,
                ];
            }
        }
        $response->{ $this->property('name') } = $files;
    }

    protected function modifyRequest(Request $request, Model $item = null)
    {
        if (is_array($request->input($this->property('name')))) {
            $oldValues = optional($item)->{ $this->property('name') };
            if (! is_array($oldValues)) {
                if (empty($oldValues)) {
                    $oldValues = [];
                } else {
                    $oldValues = [$oldValues];
                }
            }
            foreach ($oldValues as $key => $oldValue) {
                $oldValues[$key] = trim($oldValue, '/\\');
            }
            $uploadedFiles = [];
            foreach ($request->input($this->property('name')) as $fileInfo) {
                if (is_array($fileInfo) && isset($fileInfo['value'])) {
                    $fileInfo['value'] = preg_replace('/.*\/helpers\/uppy\/upload\//', '', $fileInfo['value']);
                    if (is_array(Session::get('sanjab_uppy_files.'.$fileInfo['value'])) && File::exists(Session::get('sanjab_uppy_files.'.$fileInfo['value'])['file_path'])) {
                        $fileInfo = Session::get('sanjab_uppy_files.'.$fileInfo['value']);
                        $uploadedFiles[] = new UploadedFile($fileInfo['file_path'], $fileInfo['name'], File::mimeType($fileInfo['file_path']), 0, true);
                    } elseif (in_array($fileInfo['value'], $oldValues)) {
                        $uploadedFiles[] = $fileInfo['value'];
                    }
                }
            }
            $request->merge([$this->property('name') => $uploadedFiles]);
        }
    }

    public function validationRules(Request $request, string $type, Model $item = null): array
    {
        $oldValues = optional($item)->{ $this->property('name') };
        if (! is_array($oldValues)) {
            if (empty($oldValues)) {
                $oldValues = [];
            } else {
                $oldValues = [$oldValues];
            }
        }

        $fileRules = $this->property('fileRules');
        if (! is_array($fileRules)) {
            if (empty($fileRules)) {
                $fileRules = [];
            } else {
                $fileRules = explode('|', $fileRules);
            }
        }

        $rules = [$this->name => array_merge($this->property('rules.'.$type, []), ['array', 'min:'.$this->property('min'), 'max:'.($this->property('multiple') ? $this->property('max') : 1)])];
        if (is_array($request->input($this->property('name')))) {
            foreach ($request->input($this->property('name')) as $key => $uploadedFile) {
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
}
