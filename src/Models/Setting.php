<?php

namespace Sanjab\Models;

use Sanjab\Observers\SettingObserver;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class Setting extends Model
{
    use Translatable;
    protected $table = 'sanjab_settings';

    protected $fillable = [
        'translation',
        'key',
        'name',
        'value',
    ];

    public $translatedAttributes = [
        'translated_value',
    ];

    protected $with = ['translations'];

    protected $casts = [
        'translation' => 'boolean',
        'value'       => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::observe(SettingObserver::class);
    }

    /**
     * Value getter.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        if ($this->translation) {
            return $this->translated_value;
        }

        return $this->castAttribute('value', $this->getAttributeFromArray('value'));
    }
}
