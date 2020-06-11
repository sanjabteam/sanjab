<?php

namespace Sanjab\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
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
        if ($request->isMethod('get') && $any && is_array(Session::get('sanjab_uppy_files.'.$any))) {
            if (! File::exists(Session::get('sanjab_uppy_files.'.$any)['file_path'])) {
                sleep(1);
            }

            if (File::exists(Session::get('sanjab_uppy_files.'.$any)['file_path'])) {
                if ($request->input('thumb') == 'true' && in_array(strtolower(pathinfo(Session::get('sanjab_uppy_files.'.$any)['file_path'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
                    return Image::make(Session::get('sanjab_uppy_files.'.$any)['file_path'])->fit(128, 128)->response();
                }

                return response()->file(Session::get('sanjab_uppy_files.'.$any)['file_path']);
            }
        }

        $response = app('sanjab-tus-server')->serve();
        if (! empty($response->headers->get('location'))) {
            $response->headers->set('location', rtrim(url('/'), '/').'/'.ltrim(array_get(parse_url($response->headers->get('location')), 'path'), '/'));
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
        if (in_array($disk, array_keys(config('filesystems.disks')))) {
            if (Storage::disk($disk)->exists($file)) {
                $file = Storage::disk($disk)->path($file);
                if ($request->input('thumb') == 'true' && in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
                    return Image::make($file)->fit(128, 128)->response();
                }

                return response()->file($file);
            }
        }

        return abort(404);
    }

    public static function routes(): void
    {
        Route::any('/helpers/uppy/upload/{any?}', static::class.'@upload')->name('helpers.uppy.upload')->where('any', '.*');
        Route::get('/helpers/uppy/preview', static::class.'@preview')->name('helpers.uppy.preview');
    }
}
