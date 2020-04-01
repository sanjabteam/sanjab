<?php

namespace Sanjab\Models;

use Illuminate\Database\Eloquent\Model;
use Sanjab\Observers\SettingTranslationObserver;

class SettingTranslation extends Model
{
    protected $table = 'sanjab_setting_translations';
    protected $fillable = [
        'setting_id',
        'translated_value',
    ];
    protected $casts = [
        'translated_value' => 'array',
    ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::observe(SettingTranslationObserver::class);
    }
}
