<?php

namespace Sanjab\Controllers;

use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class EditorJsController extends SanjabController
{
    public function imageUpload(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image',
        ]);
        $file = $request->file('image');
        $filename = $file->store('/', 'public');

        return ['success' => 1, 'file' => ['url' => Storage::disk('public')->url($filename)]];
    }

    public function link(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|url',
        ]);
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $request->input('url'),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ( $err){

        return ['success' => 0];
    } 
            $doc = new DOMDocument();
            @$doc->loadHTML($response);
            if ($doc->getElementsByTagName('title')->length != 0) {
                $meta = [];
                $meta['title'] = $doc->getElementsByTagName('title')->item(0)->nodeValue;

                $nodes = $doc->getElementsByTagName('link');
                for ($i = 0; $i < $nodes->length; $i++) {
                    $node = $nodes->item($i);
                    if (in_array(mb_strtolower($node->getAttribute('rel')), ['icon', 'shortcut icon'])) {
                        $meta['image'] = $node->getAttribute('href');
                    }
                }

                $nodes = $doc->getElementsByTagName('meta');
                for ($i = 0; $i < $nodes->length; $i++) {
                    $node = $nodes->item($i);
                    if (in_array(mb_strtolower($node->getAttribute('name')), ['description', 'og:description'])) {
                        $meta['description'] = $node->getAttribute('content');
                    }
                    if (mb_strtolower($node->getAttribute('name')) == 'og:image') {
                        $meta['image'] = $node->getAttribute('content');
                    }
                }

                if (isset($meta['image'])) {
                    if (! filter_var($meta['image'], FILTER_VALIDATE_URL)) {
                        $url = parse_url($request->input('url'));
                        $meta['image'] = $url['scheme'].'://'.$url['host'].'/'.ltrim($meta['image'], '/');
                    }
                    $meta['image'] = ['url' => $meta['image']];
                }

                return [
                    'success' => 1,
                    'meta' => $meta,
                ];
            }
        

        return ['success' => 0];
    }

    public static function routes(): void
    {
        Route::post('/helpers/editor-js/image-upload', static::class.'@imageUpload')->name('helpers.editor-js.image-upload');
        Route::get('/helpers/editor-js/link', static::class.'@link')->name('helpers.editor-js.link');
    }
}
