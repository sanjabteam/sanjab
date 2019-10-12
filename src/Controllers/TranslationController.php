<?php

namespace Sanjab\Controllers;

use Illuminate\Support\Facades\Route;
use Sanjab\Helpers\MenuItem;
use Sanjab\Helpers\MaterialIcons;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class TranslationController extends SanjabController
{
    /**
     * Javascript translation loader.
     *
     * @param string $locale
     * @return \Illuminate\Http\Response
     */
    public function js(string $locale)
    {
        $translationPath = sanjab_path('resources/lang/'.$locale.'/sanjab.php');
        if (File::exists($translationPath) && File::lastModified($translationPath) > Cache::get('sanjab_js_translation_last_m_'.$locale, 0)) {
            Cache::set('sanjab_js_translation_last_m_'.$locale, File::lastModified($translationPath));
            Cache::forget('sanjab_js_translation_'.$locale);
        }
        $content = Cache::rememberForEver('sanjab_js_translation_'.$locale, function () use ($locale) {
            return view("sanjab::helpers.js_translation", ['sanjabTrans' => ['sanjab::sanjab' => trans('sanjab::sanjab', [], $locale)]])->render();
        });
        return response($content)
            ->header("Content-Type", "text/javascript");
    }

    public static function routes(): void
    {
        Route::get('/helpers/translation/{locale}.js', static::class.'@js')->name('helpers.translation.js');
    }
}
