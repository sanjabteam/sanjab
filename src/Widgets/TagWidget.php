<?php

namespace Sanjab\Widgets;

use stdClass;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Tag input widget.
 *
 * @method $this asArray(bool $val)             store tags as array.
 * @method $this tagRules(array|string $val)    rules per tag.
 */
class TagWidget extends Widget
{
    protected $getters = [
        'existing-tags',
    ];
    protected $autocompleteOptions = [];

    public function init()
    {
        $this->setProperty('tag', 'tags-input');
        $this->onIndex(false);
        $this->asArray(false);
        $this->setProperty('typeahead', true);
        $this->setProperty('typeahead-style', 'dropdown');
        $this->setProperty('add-tags-on-comma', true);
        $this->setProperty('placeholder', trans('sanjab::sanjab.add_a_tag'));
        $this->tagRules([]);
        $this->viewTag('tag-view');
        $this->indexTag('tag-view');
    }

    public function postInit()
    {
        parent::postInit();
        $this->setProperty('element-id', snake_case($this->property('name')));
    }

    /**
     * Add autocomplete option.
     *
     * @param string $title
     * @return $this
     */
    public function addOption(string $value)
    {
        $this->autocompleteOptions[] = $value;

        return $this;
    }

    /**
     * Add multiple autocomplete options.
     *
     * @param array $options
     * @return $this
     */
    public function addOptions(array $options)
    {
        foreach ($options as $value) {
            $this->autocompleteOptions[] = $value;
        }

        return $this;
    }

    /**
     * Store request to model.
     *
     * @param Request $request
     * @param Model $item
     * @return void
     */
    protected function store(Request $request, Model $item)
    {
        $value = [];
        if (is_array($request->input($this->property('name')))) {
            foreach ($request->input($this->property('name')) as $tag) {
                if (is_array($tag) && isset($tag['value'])) {
                    $value[] = $tag['value'];
                }
            }
        }
        $item->{ $this->property('name') } = $this->property('asArray') ? $value : implode(',', $value);
    }

    /**
     * Modify response.
     *
     * @param stdClass $response
     * @param Model $item
     * @return void
     */
    protected function modifyResponse(stdClass $response, Model $item)
    {
        $value = $item->{ $this->property('name') };
        if (! is_array($value)) {
            if (empty($value)) {
                $value = [];
            } else {
                $value = explode(',', $value);
            }
        }
        $value = array_map(function ($tag) {
            return ['key' => $tag, 'value' => $tag];
        }, $value);
        $response->{ $this->property('name') } = $value;
    }

    /**
     * Returns validation rules.
     *
     * @param Request $request
     * @property string $type 'create' or 'edit'.
     * @property Model|null $item
     * @return array
     */
    public function validationRules(Request $request, string $type, Model $item = null): array
    {
        $tagRules = is_string($this->property('tagRules')) ? explode('|', $this->property('tagRules')) : $this->property('tagRules', []);
        $tagRules = array_merge(['distinct'], $tagRules);

        return [
            $this->name => array_merge($this->property('rules.'.$type, []), ['array']),
            $this->name.'.*.value' => $tagRules,
        ];
    }

    /**
     * Returns validation attributes.
     *
     * @param Request $request
     * @param string $type 'create' or 'edit'.
     * @param Model|null $item
     * @return array
     */
    public function validationAttributes(Request $request, string $type, Model $item = null): array
    {
        return [
            $this->name             => $this->title,
            $this->name.'.*.value'  => $this->title,
        ];
    }

    /**
     * Get existing tags formated for vue tags input.
     *
     * @return array
     */
    public function getExistingTags()
    {
        return array_map(function ($tag) {
            return ['key' => $tag, 'value' => $tag];
        }, $this->autocompleteOptions);
    }
}
