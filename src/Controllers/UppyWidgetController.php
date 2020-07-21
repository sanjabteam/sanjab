<?php

namespace Sanjab\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UppyWidgetController extends SanjabController
{
    /**
     * Save uploaded files by Uppy widget.
     *
     * @param Request $request
     * @param string $any  any unique key
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request, $any = null)
    {
        $uppyFile = session('sanjab_uppy_files.'.$any);
        if ($request->isMethod('get') && $any && is_array($uppyFile)) {
            if (! File::exists($uppyFile['file_path'])) {
                sleep(1);
            }

            if (File::exists($uppyFile['file_path'])) {
                if ($request->input('thumb') == 'true' && $this->hasImageExt($uppyFile['file_path'])) {
                    return Image::make($uppyFile['file_path'])->fit(128, 128)->response();
                }

                return response()->file($uppyFile['file_path']);
            }
        }

        $response = app('sanjab-tus-server')->serve();
        if (! empty($response->headers->get('location'))) {
            $response->headers->set('location', $this->makeLocationHeader($response));
        }

        $response->send();

        return response('', $response->getStatusCode());
    }

    /**
     * Preview uploaded file.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        $disk = $request->input('disk', 'public');
        $file = $request->input('path');
        if (! in_array($disk, array_keys(config('filesystems.disks')))) {
            abort(404);
        }

        if ($this->file($disk)->exists($file)) {
            if ($request->input('thumb') == 'true' && $this->hasImageExt($file)) {
                return Image::make($this->file($disk, $file))->fit(128, 128)->response();
            }

            return response($this->file($disk, $file))->header('Content-Type', $this->file($disk)->mimeType($file));
        }
    }

    public function file($disk, $file = null)
    {
        $f = Storage::disk($disk);

        return $file ? $f->get($file) : $f;
    }

    public static function routes(): void
    {
        Route::any('/helpers/uppy/upload/{any?}', static::class.'@upload')->name('helpers.uppy.upload')->where('any', '.*');
        Route::get('/helpers/uppy/preview', static::class.'@preview')->name('helpers.uppy.preview');
    }

    private function hasImageExt($file)
    {
        return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif',]);
    }

    private function makeLocationHeader($response)
    {
        return rtrim(url('/'), '/').'/'.ltrim(array_get(parse_url($response->headers->get('location')), 'path'), '/');
    }
}
