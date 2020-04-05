<?php

namespace Sanjab\Controllers;

use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use Sanjab\Helpers\MaterialIcons;

class IconController extends SanjabController
{
    /**
     * Show material icons.
     *
     * @return mixed
     */
    public function show()
    {
        $icons = (new ReflectionClass(MaterialIcons::class))->getConstants();
        return view('sanjab::icons', ['icons' => $icons]);
    }

    public static function routes(): void
    {
        Route::get('/icons', static::class.'@show')->name('icons');
    }
}
