<?php

namespace Sanjab\Controllers;

use Illuminate\Support\Facades\Route;
use Sanjab\Helpers\MenuItem;
use Sanjab\Helpers\MaterialIcons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;

class DropzoneController extends SanjabController
{
    public function upload(Request $request)
    {
        $allTokens = Session::get("sanjab_dropzone_js_tokens");
        if (is_array($allTokens)) {
            foreach ($allTokens as $token => $tokenValue) {
                if ($request->hasFile("__sanjab_dropzone_js_files_".$token)) {
                    $file = $request->file("__sanjab_dropzone_js_files_".$token);
                    // file validation
                    $validator = Validator::make(
                        $request->all(),
                        [
                            "__sanjab_dropzone_js_files_".$token => $tokenValue,
                        ],
                        [],
                        [
                            "__sanjab_dropzone_js_files_".$token => "فایل آپلود شده"
                        ]
                    );
                    // if fail continue loop
                    if ($validator->fails()) {
                        return response($validator->errors()->first(), 400);
                    }

                    $filename = $file->store("temp/", "local");
                    return ["filename" => $filename];
                }
            }
        }
        if ($request->has("delete_file")) {
            if (Storage::disk('local')->exists($request->input('delete_file'))) {
                Storage::disk('local')->delete($request->input('delete_file'));
            }
            return ["status" => "success"];
        }
        return response()->json(["status" => "error"], 400);
    }

    public function uploadedImageThumb(Request $request)
    {
        $disk = $request->input("disk", "local");
        if (config("filesystems.disks.".$disk) == null || !$request->filled('filename') || !Storage::disk($disk)->exists($request->input('filename'))) {
            return abort(404);
        }
        if (in_array(mb_strtolower(pathinfo($request->input('filename'), PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'])) {
            return Image::make(Storage::disk($disk)->path($request->input('filename')))->fit(150, 150)->response("jpg");
        }
        return Image::canvas(150, 150, "ff0000")->response("jpg");
    }

    public static function routes(): void
    {
        Route::post('/helpers/dropzone/handler', static::class.'@upload')->name('helpers.dropzone.upload');
        Route::get('/helpers/uploaded/thumb', static::class.'@uploadedImageThumb')->name('helpers.uploaded_image.thumb');
    }
}
