<?php

namespace Sanjab\Observers;

use Sanjab\Models\Setting;

class SettingObserver
{
    /**
     * Handle the setting "saving" event.
     *
     * @param  \Sanjab\Models\Setting  $setting
     * @return void
     */
    public function saving(Setting $setting)
    {
        $values = [];
        foreach ($setting->getAttributes() as $key => $attr) {
            if (! in_array($key, ['id', 'translation', 'key', 'name', 'value', 'created_at', 'updated_at'])) {
                $values[$key] = $attr;
                unset($setting->$key);
            }
        }

        if (count($values) > 1) {
            $values['__sanjab_multiple_attrs'] = true;
            $setting->value = $values;
        } else {
            $setting->value = array_first($values);
        }
    }
}
