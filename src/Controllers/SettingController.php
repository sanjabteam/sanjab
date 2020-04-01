<?php

namespace Sanjab\Controllers;

use Sanjab\Models\Setting;
use Illuminate\Http\Request;
use Sanjab\Helpers\MenuItem;
use Sanjab\Helpers\SearchResult;
use Sanjab\Helpers\WidgetHandler;
use Illuminate\Support\Collection;
use Sanjab\Helpers\PermissionItem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Sanjab\Helpers\SettingProperties;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

abstract class SettingController extends SanjabController
{
    use WidgetHandler;

    /**
     * Display the setting form.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $this->authorize('update_setting_'.$this->property('key'));
        $item = $this->combinedSetting(Setting::where('key', static::property('key'))->get());
        $this->initSetting($item);
        $responseItem = $this->itemResponse($item);

        return view(
            'sanjab::setting.form',
            [
                'widgets' => $this->widgets,
                'properties' => $this->properties(),
                'item' => $responseItem,
            ]
        );
    }

    /**
     * Update settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->authorize('update_setting_'.$this->property('key'));
        $combinedItem = $this->combinedSetting(Setting::where('key', static::property('key'))->get());
        $this->initSetting($combinedItem);
        $items = [];
        foreach ($this->widgets as $widget) {
            $items[$widget->property('name')] = Setting::where('key', static::property('key'))
                ->where('name', $widget->property('name'))
                ->firstOrCreate([
                    'key'         => $this->property('key'),
                    'name'        => $widget->property('name'),
                ]);
            $items[$widget->property('name')]->translation = $widget->property('translation');
            foreach ($this->combinedSetting(collect([$items[$widget->property('name')]]))->getAttributes() as $attributeName => $attributeValue) {
                $items[$widget->property('name')]->{ $attributeName } = $attributeValue;
            }
            $this->widgetsPreStore([$widget], $request, $items[$widget->property('name')]);
        }
        $this->widgetsValidate($this->widgets, $request, 'edit');

        // store data
        foreach ($this->widgets as $widget) {
            $this->widgetsStore([$widget], $request, $items[$widget->property('name')]);
        }

        // post store
        foreach ($this->widgets as $widget) {
            $this->widgetsPostStore([$widget], $request, $items[$widget->property('name')]);
            $items[$widget->property('name')]->save();
        }
        Cache::forget('sanjab_settings_'.$this->property('key'));
        Session::flash('sanjab_success', trans('sanjab::sanjab.:item_updated_successfully', ['item' => $this->property('title')]));

        return ['success' => true];
    }

    /**
     * Properties of Setting controller.
     *
     * @return SettingProperties
     */
    abstract protected static function properties(): SettingProperties;

    /**
     * Get Setting property.
     *
     * @param string $key
     * @return string|SettingProperties
     */
    final public static function property(string $key = null)
    {
        if ($key === null) {
            return static::properties();
        }

        return array_get(static::properties()->toArray(), $key);
    }

    /**
     * Initialize Setting.
     *
     * @param Model $item
     * @return void
     */
    final protected function initSetting(Model $item = null): void
    {
        if (isset($this->sanjabSettingInitialized) == false || $this->sanjabSettingInitialized == false) {
            $this->initWidgets(Setting::class);
            $this->init();
            $this->postInitWidgets('edit', $item);
            $this->sanjabSettingInitialized = true;
        }
    }

    /**
     * Combine multiple setting into one.
     *
     * @param Collection $settings
     * @return Setting
     */
    protected function combinedSetting(Collection $settings)
    {
        $out = new Setting;
        foreach ($settings as $setting) {
            if ($setting->translation) {
                foreach (array_keys(config('sanjab.locales')) as $locale) {
                    if (is_array($setting->translateOrNew($locale)->translated_value) && isset($setting->translateOrNew($locale)->translated_value['__sanjab_multiple_attrs']) && $setting->translateOrNew($locale)->translated_value['__sanjab_multiple_attrs'] == true) {
                        foreach ($out->translateOrNew($locale)->{$setting->name} as $key => $value) {
                            $out->translateOrNew($locale)->{$key} = $setting->translateOrNew($locale)->translated_value[$key];
                        }
                    } else {
                        $out->translateOrNew($locale)->{$setting->name} = $setting->translateOrNew($locale)->translated_value;
                    }
                }
            } else {
                if (is_array($setting->value) && isset($setting->value['__sanjab_multiple_attrs']) && $setting->value['__sanjab_multiple_attrs'] == true) {
                    foreach ($setting->value as $key => $value) {
                        if ($key != '__sanjab_multiple_attrs') {
                            $out->{$key} = $setting->value[$key];
                        }
                    }
                } else {
                    $out->{$setting->name} = $setting->value;
                }
            }
        }

        return $out;
    }

    /**
     * Using to override initialize.
     *
     * @return void
     */
    abstract protected function init(): void;

    public static function routes(): void
    {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get(static::property('key'), static::class.'@show')->name(static::property('key'));
            Route::post(static::property('key'), static::class.'@update')->name(static::property('key'));
        });
    }

    public static function menus(): array
    {
        return [
            MenuItem::create('javascript:void(0);')
                ->title(trans('sanjab::sanjab.settings'))
                ->icon('settings')
                ->active(function () {
                    return Route::is('sanjab.settings.*');
                })
                ->addChild(
                    MenuItem::create(route('sanjab.settings.'.static::property('key')))
                    ->title(static::property('title'))
                    ->icon(static::property('icon'))
                    ->badge(static::property('badge'))
                    ->badgeVariant(static::property('badgeVariant'))
                    ->hidden(function () {
                        return Auth::user()->cannot('update_setting_'.static::property('key'));
                    })
                ),
        ];
    }

    public static function permissions(): array
    {
        $permission = PermissionItem::create(trans('sanjab::sanjab.settings'))
                        ->addPermission(trans('sanjab::sanjab.edit_:item', ['item' => static::property('title')]), 'update_setting_'.static::property('key'));

        return [$permission];
    }

    public static function globalSearch(string $search)
    {
        if (Auth::user()->can('update_setting_'.static::property('key')) && static::property('globalSearch')) {
            $controllerInsatance = app(static::class);
            App::call([$controllerInsatance, 'show']);

            if (preg_match('/.*'.preg_quote($search).'.*/', static::property('title'))) {
                return [
                    SearchResult::create(static::property('title'), route('sanjab.settings.'.static::property('key')))
                                            ->icon(static::property('icon'))
                                            ->order(50),
                ];
            }

            foreach ($controllerInsatance->widgets as $widget) {
                if (preg_match('/.*'.preg_quote($search).'.*/', $widget->title)) {
                    return [
                        SearchResult::create(trans('sanjab::sanjab.:item_in_:part', ['item' => $widget->title, 'part' => static::property('title')]), route('sanjab.settings.'.static::property('key')))
                                                ->icon(static::property('icon'))
                                                ->order(50),
                    ];
                }
            }
        }

        return [];
    }
}
