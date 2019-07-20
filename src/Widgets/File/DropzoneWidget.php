<?php

namespace Sanjab\Widgets\File;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Sanjab\Widgets\Widget;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use stdClass;

/**
 * Dropzone image upload
 *
 * @method $this    extensions(array $val)                  array of allowed extensions
 * @method $this    extension(string $val)                  extention to save
 * @method $this    disk(string $val)                       upload disk
 * @method $this    max(integer $val)                       max count of images
 * @method $this    maxSize(integer $val)                   max file upload size
 * @method $this    filename(callable $val)                 function to generate random file name; parameters ()
 * @method $this    fileStoreCallBack(callable $val)        function to store image and return address; parameters ($fileContent, $directory, $filename, $disk)
 * @method $this    width(integer $val)                     width image to resize
 * @method $this    height(integer $val)                    width image to resize
 * @method $this    directory(string $val)                  directory to save.
 */
class DropzoneWidget extends Widget
{
    public function init()
    {
        $this->onIndex(false)->sortable(false)->searchable(false)->all(false)->disk("public")->maxSize(4096)->max(10);
        $this->tag("dropzone-widget")->viewTag('dropzone-view');
        $this->directory(now()->year.'/'.now()->month);
        $this->extensions(['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg']);
        $this->filename(function () {
            return Str::random(40);
        });
        $this->fileStoreCallBack(function ($fileContent, $directory, $filename, $disk) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if (in_array(mb_strtolower($extension), ['jpg', 'jpeg', 'png', 'bmp'])) {
                $filename = pathinfo($filename, PATHINFO_DIRNAME)."/".pathinfo($filename, PATHINFO_FILENAME);
                if ($this->extension) {
                    $extension = $this->extension;
                }
                if (empty($directory) == false) {
                    $filename = $directory . "/" . $filename;
                }
                $filename = $filename.'.'.$extension;
                $image = Image::make($fileContent)->limitColors(127);
                if ($extension == "jpeg" || $extension == "jpg") {
                    $image->interlace();
                }
                if ($this->property("width") && $this->property("height")) {
                    $image->fit($this->property("width"), $this->property("height"));
                } elseif ($this->property("width")) {
                    $image->widen($this->property("width"));
                } elseif ($this->property("height")) {
                    $image->heighten($this->property("height"));
                }
                $fileContent = $image->encode($extension);
            }
            Storage::disk($disk)->put($filename, $fileContent);
            return $filename;
        });
    }

    protected function store(Request $request, Model $item)
    {
        $result = [];
        $oldValue = $item->{ $this->property("name") };
        if (is_array($request->input($this->property("name")))) {
            foreach ($request->input($this->property("name")) as $file) {
                $filename = (($this->property("filename"))()) . '.' . File::extension($file);
                if (Storage::disk($this->property("disk"))->exists($file)) {
                    foreach ($oldValue as $vkey => $valueFile) {
                        if (is_array($valueFile)) {
                            if (count($valueFile) == 0) {
                                continue;
                            }
                            $valueFile = array_first($valueFile);
                        }
                        if ($valueFile == $file) {
                            $result[] = $oldValue[$vkey];
                            break;
                        }
                    }
                } else {
                    if (Storage::disk("local")->exists($file)) {
                        $result[] = ($this->property("fileStoreCallBack"))(Storage::disk("local")->get($file), $this->property("directory"), $filename, $this->property("disk"));
                        Storage::disk("local")->delete($file);
                    }
                }
            }
        }
        $item->{$this->property("name")} = $result;
        $allTokens = Session::get("sanjab_dropzone_js_tokens");
        unset($allTokens[$this->token]);
        Session::put('sanjab_dropzone_js_tokens', $allTokens);
    }

    public function postInit()
    {
        if ($__token = request()->input('__sanjab_dropzone_js_token_'.$this->property("name"))) {
            if (in_array($__token, array_keys(Session::get("sanjab_dropzone_js_tokens")))) {
                $this->setProperty("token", $__token);
            }
        }
        // if there is no token. generate new one.
        if (empty($this->property("token"))) {
            $this->setProperty("token", time().'_'.uniqid());
        }
        $allTokens = Session::get("sanjab_dropzone_js_tokens");
        $allTokens[$this->property("token")] = ['file', 'mimes:'.implode(",", $this->property("extensions")), 'max:'.$this->property("maxSize")];
        Session::put('sanjab_dropzone_js_tokens', $allTokens);
        $this->property('rules', [
            'create' =>  [
                $this->property("name")       => ['array', 'max:'.$this->property("max")],
                $this->property("name").".*"  => ['string', 'min:1'],
            ],
            'edit' => [
                $this->property("name")       => ['array', 'max:'.$this->property("max")],
                $this->property("name").".*"  => ['string', 'min:1'],
            ]
        ]);
    }

    protected function modifyResponse(stdClass $response, Model $item)
    {
        $response->{ $this->property("name") } = [];
        if (is_array($item->{ $this->property("name") })) {
            foreach ($item->{ $this->property("name") } as $img) {
                if ($img && Storage::disk($this->property("disk"))->exists($img)) {
                    $response->{ $this->property("name") }[] = [
                        'name' => $img,
                        'url' => route("sanjab.helpers.uploaded_image.thumb", ["filename" => $img, "disk" => $this->property("disk")]),
                        'original' => Storage::disk($this->property("disk"))->url($img),
                        'size' => Storage::disk($this->property("disk"))->size($img)
                    ];
                }
            }
        }
    }

    public function onDeleting(Model $data)
    {
        $dataToDelete = $data;
        if (is_array($dataToDelete->{$this->property("name")}) && count($dataToDelete->{$this->property("name")}) > 0) {
            foreach ($dataToDelete->{$this->property("name")} as $oldFiles) {
                if (! is_array($oldFiles)) {
                    $oldFiles = [$oldFiles];
                }
                foreach ($oldFiles as $oldFile) {
                    if (Storage::disk($this->property("disk"))->exists($oldFile)) {
                        Storage::disk($this->property("disk"))->delete($oldFile);
                    }
                }
            }
        }
    }

    public function onUpdating(Model $data)
    {
        $oldFiles = $data->getOriginal()[$this->property("name")];
        $newFiles = $data->{ $this->property("name") };
        if (! is_array($oldFiles)) {
            $oldFiles = json_decode($oldFiles);
        }
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
