<?php

namespace Sanjab\Observers;

use Sanjab\Models\SettingTranslation;

class SettingTranslationObserver
{
    /**
     * Handle the setting "saving" event.
     *
     * @param  \Sanjab\Models\Setting  $setting
     * @return void
     */
    public function saving(SettingTranslation $setting)
    {
        $values = [];
        foreach ($setting->getAttributes() as $key => $attr) {
            if (! in_array($key, ['id', 'locale', 'setting_id', 'translated_value'])) {
                $values[$key] = $attr;
                unset($setting->$key);
            }
        }

        if (count($values) > 1) {
            $values['__sanjab_multiple_attrs'] = true;
            $setting->translated_value = $values;
        } else {
            $setting->translated_value = array_first($values);
        }
    }
}
