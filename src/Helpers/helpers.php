<?php

if (! function_exists('sanjab_mix')) {
    /**
     * Get path of versioned out put of package mix.
     *
     * @param  string  $path
     * @return \Illuminate\Support\HtmlString|string
     *
     * @throws \Exception
     */
    function sanjab_mix($path)
    {
        return mix($path, "vendor/sanjab");
    }
}

if (! function_exists('sanjab_path')) {
    /**
     * Get path of sanjab.
     *
     * @param  string  $path
     * @return string
     *
     * @throws \Exception
     */
    function sanjab_path($path)
    {
        return realpath(__DIR__.'/../../'.ltrim($path, '\/\\'));
    }
}

if (! function_exists("sanjab_setting")) {
    /**
     * Get setting value with name
     *
     * @param string $name key of setting  "group.name"
     * @param mixed $default  default value if setting doent exists
     * @param string $locale  locale name for translated settings.
     * @return mixed
     */
    function sanjab_setting(string $name, $default = null, string $locale = null)
    {
        $out = $default;
        $name = explode(".", $name);
        $key = $name[0];
        unset($name[0]);
        $name = implode(".", $name);
        $data = \Cache::rememberForever("sanjab_settings_".$key, function () use ($key) {
            return \Sanjab\Models\Setting::where('key', $key)->get()->keyBy("name");
        });
        if (empty($name)) {
            return $data;
        }
        if (isset($data[$name])) {
            $out = $data[$name]->value;
            if ($data[$name]->translation && $locale) {
                $out = optional($data[$name]->translate($locale))->translated_value;
            }
        }
        return $out;
    }
}

if (! function_exists("sanjab_set_setting")) {
    /**
     * Set setting value with name.
     *
     * @param string $name  key of setting  "group.name"
     * @param mixed $value  value of setting
     * @param string $locale locale just for translated settings.
     * @return void
     */
    function sanjab_set_setting(string $name, $value = null, string $locale = null)
    {
        $name = explode(".", $name);
        $key = $name[0];
        unset($name[0]);
        $name = implode(".", $name);
        $setting = \Sanjab\Models\Setting::where('key', $key)->where('name', $name)->firstOrCreate([
            'key' => $key,
            'name' => $name
        ]);
        if ($locale) {
            $setting->translation = true;
            $setting->translateOrNew($locale)->{ $name } = $value;
        } else {
            $setting->translation = false;
            $setting->{ $name } = $value;
        }
        $setting->save();
        \Cache::forget("sanjab_settings_".$key);
    }
}

if (! function_exists("setting")) {
    /**
     * Get setting value with name
     *
     * @param string $name key of setting  "group.name"
     * @param mixed $default  default value if setting doent exists
     * @param string $locale  locale name for translated settings.
     * @return mixed
     */
    function setting(string $name, $default = null, string $locale = null)
    {
        return sanjab_setting($name, $default, $locale);
    }
}

if (! function_exists("set_setting")) {
    /**
     * Set setting value with name.
     *
     * @param string $name  key of setting  "group.name"
     * @param mixed $value  value of setting
     * @param string $locale locale just for translated settings.
     * @return void
     */
    function set_setting(string $name, $value = null, string $locale = null)
    {
        return sanjab_set_setting($name, $value, $locale);
    }
}
