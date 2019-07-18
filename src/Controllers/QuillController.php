<?php

namespace Sanjab\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuillController extends SanjabController
{
    public function imageUpload(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image'
        ]);
        $file = $request->file('file');
        $filename = $file->store("/", "public");
        return response()->json(Storage::disk('public')->url($filename));
    }

    public static function routes(): void
    {
        Route::post('/helpers/quill/image-upload', static::class.'@imageUpload')->name('helpers.quill.image-upload');
    }
}
