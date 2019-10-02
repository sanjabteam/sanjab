<?php

namespace Sanjab\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use stdClass;
use Illuminate\Validation\Validator;
use Sanjab\Exceptions\CrudTypeNotAllowed;

trait WidgetHandler
{
    /**
     * Array of widgets.
     *
     * @var \Sanjab\Widgets\Widget[]
     */
    protected $widgets = [];

    /**
     * Initialize widgets and event handlers.
     *
     * @param string $model
     * @return void
     */
    public function initWidgets(string $model)
    {
        $events = ['retrieved', 'creating', 'created', 'updating', 'updated', 'saving', 'saved', 'deleting', 'deleted', 'restoring', 'restored'];
        foreach ($events as $event) {
            if (method_exists($model, $event)) {
                ($model)::{$event}(function (Model $item) use ($event) {
                    $this->{'on'.title_case($event)}($item);
                    foreach ($this->widgets as $widget) {
                        if (! in_array($event, ['deleting', 'deleted']) || isset($item->forceDeleting) == false || $item->forceDeleting) {
                            $widget->{'on'.title_case($event)}($item);
                        } else {
                            $widget->{'onSoft'.title_case($event)}($item);
                        }
                    }
                    if (in_array($event, ['saving', 'deleting', 'restoring'])) {
                        $this->onChanging($item);
                    }
                    if (in_array($event, ['saved', 'deleted', 'restored'])) {
                        $this->onChanged($item);
                    }
                });
            }
        }
    }

    /**
     * Post initialize after widget ready.
     *
     * @param string $type
     * @param Model|null $item
     * @return void
     */
    public function postInitWidgets(string $type, Model $item = null)
    {
        foreach ($this->widgets as $key => $widget) {
            $this->widgets[$key]->controllerProperties = $this->properties()->toArray();
            $this->widgets[$key]->controllerProperties['controller'] = static::class;
            $this->widgets[$key]->controllerProperties['type'] = $type;
            $this->widgets[$key]->controllerProperties['item'] = $item;
            $this->widgets[$key]->postInit();
            $this->widgets[$key]->postInitSearchWidgets();
        }
    }

    /**
     * Validation rules
     *
     * @param Request $request
     * @param string $type  create|edit
     * @param Model|null $item
     * @return array
     */
    public function validationRules(Request $request, string $type, Model $item = null)
    {
        return [];
    }

    /**
     * Validation attributes
     *
     * @param Request $request
     * @param string $type  create|edit
     * @param Model|null $item
     * @return array
     */
    public function validationAttributes(Request $request, string $type, Model $item = null)
    {
        return [];
    }

    /**
     * Validation messages.
     *
     * @param Request $request
     * @param string $type  create|edit
     * @param Model|null $item
     * @return array
     */
    public function validationMessages(Request $request, string $type, Model $item = null)
    {
        return [];
    }

    /**
     * Validation after callback.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @param \Illuminate\Http\Request $request
     * @param string $type  create|edit
     * @param Model|null  $item
     * @return array
     */
    public function validationAfter(Validator $validator, Request $request, string $type, Model $item = null)
    {
        return [];
    }

    /**
     * Save request to item model
     *
     * @param Request $request
     * @param Model $item
     * @param string $type create|edit
     * @return void
     */
    public function save(Request $request, Model $item, string $type)
    {
        $widgets = array_filter($this->widgets, function ($widget) use ($type) {
            return $widget->property('on'.$this->typeTitleCase($type));
        });
        $this->widgetsPreStore($widgets, $request, $item);
        $this->widgetsValidate($widgets, $request, $type, $type == 'edit' ? $item : null);
        $this->widgetsStore($widgets, $request, $item);
        $item->save();
        $this->widgetsPostStore($widgets, $request, $item);
        $item->save();
    }

    /**
     * Do prestore
     *
     * @param array $widgets
     * @param Request $request
     * @param Model $item
     * @return void
     */
    public function widgetsPreStore(array $widgets, Request $request, Model $item)
    {
        $this->modifyRequest($request, $item);

        foreach ($widgets as $widget) {
            if (! $widget->property('translation')) {
                $widget->doModifyRequest($request, $item);
            }
        }
        foreach ($widgets as $widget) {
            if (! $widget->property('translation')) {
                $widget->doPreStore($request, $item);
            }
        }

        foreach (array_keys(config('sanjab.locales')) as $locale) {
            $translatedRequest = $this->translatedRequest($request, $locale);
            foreach ($widgets as $widget) {
                if ($widget->property('translation')) {
                    $widget->doModifyRequest($translatedRequest, $item);
                }
            }
            foreach ($widgets as $widget) {
                if ($widget->property('translation')) {
                    $widget->doPreStore($translatedRequest, $item->translateOrNew($locale));
                }
            }
        }
    }

    /**
     * Do store
     *
     * @param array $widgets
     * @param Request $request
     * @param Model $item
     * @return void
     */
    public function widgetsStore(array $widgets, Request $request, Model $item)
    {
        foreach ($widgets as $widget) {
            if (! $widget->property('translation')) {
                $widget->doStore($request, $item);
            }
        }

        foreach (array_keys(config('sanjab.locales')) as $locale) {
            $translatedRequest = $this->translatedRequest($request, $locale);
            foreach ($widgets as $widget) {
                if ($widget->property('translation')) {
                    $widget->doStore($translatedRequest, $item->translateOrNew($locale));
                }
            }
        }
    }

    /**
     * Do post store
     *
     * @param array $widgets
     * @param Request $request
     * @param Model $item
     * @return void
     */
    public function widgetsPostStore(array $widgets, Request $request, Model $item)
    {
        foreach ($widgets as $widget) {
            if (! $widget->property('translation')) {
                $widget->doPostStore($request, $item);
            }
        }

        foreach (array_keys(config('sanjab.locales')) as $locale) {
            $translatedRequest = $this->translatedRequest($request, $locale);
            foreach ($widgets as $widget) {
                if ($widget->property('translation')) {
                    $widget->doPostStore($translatedRequest, $item->translateOrNew($locale));
                }
            }
        }
    }

    /**
     * Do validate.
     *
     * @param array $widgets
     * @param Request $request
     * @param string $type
     * @param Model|null $item
     * @return void
     */
    public function widgetsValidate(array $widgets, Request $request, string $type, Model $item = null)
    {
        $messages = $this->validationMessages($request, $type, $item);
        $attributes = $this->validationAttributes($request, $type, $item);
        $rules = $this->validationRules($request, $type, $item);
        foreach ($rules as $key => $rule) {
            if (is_string($rule)) {
                $rules[$key] = explode("|", $rule);
            }
        }

        // fill for tranlated rules
        foreach (['attributes', 'messages'] as $varName) {
            if (! isset(${$varName}['sanjab_translations']) || ! is_array(${$varName}['sanjab_translations'])) {
                ${$varName}['sanjab_translations'] = [];
            }
            foreach (array_keys(config('sanjab.locales')) as $locale) {
                if (! isset(${$varName}['sanjab_translations'][$locale]) || ! is_array(${$varName}['sanjab_translations'][$locale])) {
                    ${$varName}['sanjab_translations'][$locale] = [];
                }
            }
        }

        foreach ($widgets as $widget) {
            if ($widget->property('translation')) {
                foreach (config('sanjab.locales') as $locale => $localeName) {
                    foreach ($widget->validationRules($request, $type, $item) as $key => $rule) {
                        if (! isset($rules['sanjab_translations'][$locale][$key])) {
                            $rules['sanjab_translations.'.$locale.'.'.$key] = [];
                        }
                        $rules['sanjab_translations.'.$locale.'.'.$key] = array_values(array_unique(array_merge($rules['sanjab_translations.'.$locale.'.'.$key], $widget->validationRules($request, $type, $item)[$key])));
                    }
                    $messages['sanjab_translations'][$locale] = array_merge($messages['sanjab_translations'][$locale], $widget->validationMessages($request, $type, $item));
                    $attributes['sanjab_translations'][$locale] = array_merge(
                        $attributes['sanjab_translations'][$locale],
                        array_map(function ($attr) use ($localeName) {
                            return $attr.' ('.$localeName.')';
                        }, $widget->validationAttributes($request, $type, $item))
                    );
                }
            } else {
                foreach ($widget->validationRules($request, $type, $item) as $key => $rule) {
                    if (! isset($rules[$key])) {
                        $rules[$key] = [];
                    }
                    $rules[$key] = array_values(array_unique(array_merge($rules[$key], $rule)));
                }
                $messages = array_merge($messages, $widget->validationMessages($request, $type, $item));
                $attributes = array_merge($attributes, $widget->validationAttributes($request, $type, $item));
            }
        }
        $messages = array_dot($messages);
        $attributes = array_dot($attributes);
        $validator = \Validator::make($request->all(), $rules, $messages, $attributes);
        $validator->after(function ($validator) use ($type, $item, $request) {
            $this->validationAfter($validator, $request, $type, $item);
        });
        $validator->validate();
    }

    /**
     * To get title case of create and edit and validate them.
     *
     * @param string $type
     * @return void
     */
    private function typeTitleCase(string $type)
    {
        if ($type != 'create' && $type != 'edit') {
            throw new CrudTypeNotAllowed();
        }

        return title_case($type);
    }

    /**
     * Modify request before validate and store.
     *
     * @param Request $request
     * @param Model|null $item
     * @return void
     */
    protected function modifyRequest(Request $request, Model $item = null)
    {
    }

    /**
     * Modify response sending to view or edit.
     *
     * @param object $respones
     * @param Model $item
     * @return void
     */
    protected function modifyResponse(stdClass $response, Model $item)
    {
    }

    /**
     * Get request only for translated part using for translation widgets.
     *
     * @param Request $request
     * @param string $locale
     * @return Request
     */
    protected function translatedRequest(Request $request, string $locale)
    {
        $translatedRequest = Request::create(
            $request->getUri(),
            $request->method(),
            is_array($request->input('sanjab_translations.'.$locale)) ? $request->input('sanjab_translations.'.$locale) : [],
            $request->cookie(),
            is_array($request->file('sanjab_translations.'.$locale)) ? $request->file('sanjab_translations.'.$locale) : [],
            $request->server()
        );
        $translatedRequest->replace(is_array($request->input('sanjab_translations.'.$locale)) ? $request->input('sanjab_translations.'.$locale) : []);
        return $translatedRequest;
    }

    /**
     * Convert model item to response array.
     *
     * @param Model $item
     * @return array
     */
    protected function itemResponse(Model $item)
    {
        $responseItem = new stdClass;
        $responseItem->id = $item->id;
        $responseItem->sanjab_translations = [];
        foreach (array_keys(config('sanjab.locales')) as $locale) {
            $responseItem->sanjab_translations[$locale] = new stdClass;
        }
        foreach ($this->widgets as $widget) {
            if ($widget->property('translation')) {
                $widget->doModifyResponse($responseItem, $item);
                foreach (array_keys(config('sanjab.locales')) as $locale) {
                    if ($item->translate($locale) != null) {
                        $widget->doModifyResponse($responseItem->sanjab_translations[$locale], $item->translate($locale));
                    }
                }
            } else {
                $widget->doModifyResponse($responseItem, $item);
            }
        }
        $this->modifyResponse($responseItem, $item);
        if (isset($this->actions)) {
            $responseItem->__can = [];
            $responseItem->__action_url = [];
            foreach ($this->actions as $key2 => $action) {
                if ($action->perItem) {
                    $responseItem->__can[$key2] = ($action->property('authorize'))($item);
                    if ($action->url) {
                        $responseItem->__action_url[$key2] = $action->getActionUrl($item);
                    }
                }
            }
        }
        return (array)$responseItem;
    }

    /**
     * Widgets getter.
     *
     * @return array
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    protected function onRetrieved(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    protected function onCreating(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    protected function onCreated(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    protected function onUpdating(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    protected function onUpdated(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    protected function onSaving(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    protected function onSaved(Model $item)
    {
    }

    /**
     * Model event ( not for soft delete )
     *
     * @param Model $item
     * @return void
     */
    protected function onDeleting(Model $item)
    {
    }

    /**
     * Model event ( not for soft delete )
     *
     * @param Model $item
     * @return void
     */
    protected function onDeleted(Model $item)
    {
    }

    /**
     * Model event ( for soft deletes only )
     *
     * @param Model $item
     * @return void
     */
    protected function onSoftDeleting(Model $item)
    {
    }

    /**
     * Model event ( for soft deletes only )
     *
     * @param Model $item
     * @return void
     */
    protected function onSoftDeleted(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    protected function onRestoring(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    protected function onRestored(Model $item)
    {
    }

    /**
     * Model event when any change happening to database
     *
     * @param Model $item
     * @return void
     */
    protected function onChanging(Model $item)
    {
    }

    /**
     * Model event when any change happend to database
     *
     * @param Model $item
     * @return void
     */
    protected function onChanged(Model $item)
    {
    }
}
