<?php

namespace Sanjab\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Sanjab\Sanjab;

class SearchController extends SanjabController
{
    /**
     * Perform global search.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $searchResults = [];
        if ($request->filled('search')) {
            $searchResults = Sanjab::search($request->input('search'));
        }
        if ($request->wantsJson()) {
            return $searchResults;
        }
    }

    public static function routes(): void
    {
        Route::get('/search', static::class.'@search')->name('search.global');
    }
}
