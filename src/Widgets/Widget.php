<?php

namespace Sanjab\Widgets;

use stdClass;
use Sanjab\Helpers\PropertiesHolder;
use Sanjab\Helpers\TableColumn;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Sanjab\Helpers\SearchType;

/**
 * Base class for all widgets ( form fields and table cells and view )
 *
 * @method $this    onIndex(boolean $val)                       is this element avilble on index.
 * @method $this    onView(boolean $val)                        is this element avilble on view.
 * @method $this    onCreate(boolean $val)                      is this element avilble on create.
 * @method $this    onEdit(boolean $val)                        is this element avilble on edit.
 * @method $this    onStore(boolean $val)                       should this store in database.
 * @method $this    sortable(boolean $val)                      is this widget sortable.
 * @method $this    searchable(boolean $val)                    is this widget searchable.
 * @method $this    customStore(callable $val)                  store with custom method -  parameters : ($request, $item).
 * @method $this    customPreStore(callable $val)               pre store with custom method -  parameters : ($request, $item).
 * @method $this    customPostStore(callable $val)              post store with custom method -  parameters : ($request, $item).
 * @method $this    customModifyResponse(callable $val)         custom item response modifyer -  parameters : ($response, $item).
 * @method $this    customModifyRequest(callable $val)          custom request modify -  parameters : ($request, $item).
 * @method $this    value(mixed $val)                           default value for input.
 * @method $this    name(string $val)                           field name.
 * @method $this    title(string $val)                          field title.
 * @method $this    description(string $val)                    field description.
 * @method $this    indexTag(string $val)                       field default tag in table columns.
 * @method $this    viewGroupTag(string $val)                   field default tag in show page.
 * @method $this    viewTag(string $val)                        field default tag in show page.
 * @method $this    tag(string $val)                            field tag.
 * @method $this    groupTag(string $val)                       field group tag.
 * @method $this    class(string $val)                          class of input field.
 * @method $this    cols(string $val)                           bootstrap based column width.
 */
abstract class Widget extends PropertiesHolder
{
    public $controllerProperties = [];

    public function __construct(array $properties = [])
    {
        $this->onCreate(true)->onEdit(true)->onIndex(true)->onStore(true)
            ->onView(true)->col(12)->searchable(true)->sortable(true)
            ->indexTag('simple-view')->viewTag('simple-view')->viewGroupTag('simple-view-group')->translation(false)
            ->groupTag("simple-group")->tag("input")->cols(12);
        parent::__construct($properties);
        $this->init();
    }

    /**
     * create new widget.
     *
     * @return static
     */
    final public static function create($name = null, $title = null)
    {
        $out = new static();
        if ($name) {
            $out->name($name);
        }
        if (empty($title)) {
            $out->title(str_replace('_', ' ', Str::title($name)));
        } else {
            $out->title($title);
        }

        return $out;
    }

    /**
     * Called when widget created.
     *
     * @return void
     */
    abstract public function init();

    /**
     * Called when all widgets has been created.
     *
     * @return void
     */
    public function postInit()
    {
    }

    /**
     * Get table columns.
     *
     * @return TableColumn[]
     */
    final public function getTableColumns()
    {
        if ($this->property("onIndex")) {
            return $this->tableColumns();
        }
        return [];
    }

    /**
     * To override table columns creating by this item.
     *
     * @return TableColumn[]
     */
    protected function tableColumns()
    {
        return [
            TableColumn::create($this->property("name"))
                ->title($this->property("title"))
                ->sortable($this->property('sortable'))
                ->tag($this->property('indexTag'))
        ];
    }

    /**
     * Get search types.
     *
     * @return null|array|SearchType[]
     */
    final public function getSearchTypes()
    {
        if ($this->property('searchable')) {
            $searchTypes = $this->searchTypes();
            if (count($searchTypes) != 0) {
                return $searchTypes;
            }
        }
        return null;
    }

    /**
     * Get search types.
     *
     * @return array|SearchType[]
     */
    protected function searchTypes(): array
    {
        return [
            SearchType::create('equal', trans('sanjab::sanjab.equal'))
                        ->addWidget(TextWidget::create('equal', trans('sanjab::sanjab.equal'))),
            SearchType::create('not_equal', trans('sanjab::sanjab.not_equal'))
                        ->addWidget(TextWidget::create('not_equal', trans('sanjab::sanjab.not_equal'))),
            SearchType::create('similar', trans('sanjab::sanjab.similar'))
                        ->addWidget(TextWidget::create('similar', trans('sanjab::sanjab.similar'))),
            SearchType::create('not_similar', trans('sanjab::sanjab.not_similar'))
                        ->addWidget(TextWidget::create('not_similar', trans('sanjab::sanjab.not_similar'))),
        ];
    }

    /**
     * To do search.
     *
     * @param Builder $query
     * @param string $search
     * @param string $type
     * @return void
     */
    final public function doSearch(Builder $query, string $search, string $type = null)
    {
        if ($this->property("searchable") && !empty($search)) {
            $this->search($query, $search, $type);
        }
    }

    /**
     * To override search query modify.
     *
     * @param Builder $query
     * @param string $search
     * @param string $type
     * @return void
     */
    protected function search(Builder $query, string $search, string $type = null)
    {
        switch ($type) {
            case 'exact':
                $query->where($this->property('name'), 'LIKE', $search);
                break;
            default:
                $query->where($this->property('name'), 'LIKE', '%'.$search.'%');
                break;
        }
    }

    /**
     * To do sort.
     *
     * @param Builder $query
     * @param string $key
     * @param string $direction "asc" or "desc"
     * @return void
     */
    final public function doOrder(Builder $query, string $key, string $direction = 'asc')
    {
        if ($this->property('sortable')) {
            $this->order($query, $key, $direction);
        }
    }

    /**
     * To overide sort query modify.
     *
     * @param Builder $query
     * @param string $key
     * @param string $direction
     * @return void
     */
    protected function order(Builder $query, string $key, string $direction = 'asc')
    {
        $query->orderBy($this->property('name'), $direction);
    }

    /**
     * Do Store request to model!
     *
     * @param Request $request
     * @param Model $item
     * @return void
     */
    public function doStore(Request $request, Model $item)
    {
        if ($this->property("onStore")) {
            if ($this->property("customStore")) {
                return ($this->property("customStore"))($request, $item);
            }
            return $this->store($request, $item);
        }
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
        $item->{ $this->property("name") } = $request->input($this->property("name"));
    }

    /**
     * To store on model before validation. for manage temp values and ... if valiadtion faild store will not called
     *
     * @param Request $request
     * @param Model $item
     * @return void
     */
    public function doPreStore(Request $request, Model $item)
    {
        if ($this->property("onStore")) {
            if ($this->property("customPreStore")) {
                return ($this->property("customPreStore"))($request, $item);
            }
            return $this->preStore($request, $item);
        }
    }

    /**
     * To override pre store on model
     *
     * @param Request $request
     * @param Model $item
     * @return void
     */
    protected function preStore(Request $request, Model $item)
    {
    }

    /**
     * Do Store request to model after save!
     *
     * @param Request $request
     * @param Model $item
     * @return void
     */
    public function doPostStore(Request $request, Model $item)
    {
        if ($this->property("onStore")) {
            if ($this->property("customPostStore")) {
                return ($this->property("customPostStore"))($request, $item);
            }
            return $this->postStore($request, $item);
        }
    }

    /**
     * Store request to model after save.
     *
     * @param Request $request
     * @param Model $item
     * @return void
     */
    protected function postStore(Request $request, Model $item)
    {
    }

    /**
     * Do modifying request.
     *
     * @param Request $request
     * @param Model|null $item
     * @return void
     */
    final public function doModifyRequest(Request $request, Model $item = null)
    {
        if (is_callable($this->property('customModifyRequest'))) {
            return ($this->property('customModifyRequest'))($request, $item);
        }
        $this->modifyRequest($request, $item);
    }

    /**
     * Do modifying model response.
     *
     * @param object $respones
     * @param Model $item
     * @return void
     */
    final public function doModifyResponse(stdClass $response, Model $item)
    {
        if (is_callable($this->property('customModifyResponse'))) {
            return ($this->property('customModifyResponse'))($response, $item);
        }
        $this->modifyResponse($response, $item);
    }


    /**
     * To override modifying request.
     *
     * @param Request $request
     * @param null|Model $item
     * @return void
     */
    protected function modifyRequest(Request $request, Model $item = null)
    {
    }

    /**
     * To override modifying model response.
     *
     * @param object $respones
     * @param Model $item
     * @return void
     */
    protected function modifyResponse(stdClass $response, Model $item)
    {
        $response->{ $this->property("name") } = $item->{ $this->property("name") };
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
            $this->name         => $this->title,
            $this->name.'.*'    => $this->title,
        ];
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
        return [
            $this->name => $this->property('rules.'.$type, []),
        ];
    }

    /**
     * Returns validation messages.
     *
     * @param Request $request
     * @property string $type 'create' or 'edit'.
     * @property Model|null $item
     * @return array
     */
    public function validationMessages(Request $request, string $type, Model $item = null): array
    {
        return [];
    }

    /**
     * Add custom validation rules.
     *
     * @param string|array  $rules
     * @param string $type  'create' or 'edit'
     * @return $this
     */
    final public function rules($rules, $type = null)
    {
        if ($type != 'create' && $type != 'edit') {
            $this->rules($rules, 'create');
            return $this->rules($rules, 'edit');
        }

        if (! is_array($rules)) {
            $rules = explode('|', $rules);
        }
        $thisRules = $this->property('rules', ['create' => [], 'edit' => []]);
        $thisRules[$type] = array_merge($thisRules[$type], $rules);
        $this->setProperty('rules', $thisRules);
        return $this;
    }

    /**
     * Add validation rules for create only.
     *
     * @param string|array  $rules
     * @return $this
     */
    final public function createRules($rules)
    {
        return $this->rules($rules, 'create');
    }

    /**
     * Add validation rules for edit only.
     *
     * @param string|array  $rules
     * @return $this
     */
    final public function editRules($rules)
    {
        return $this->rules($rules, 'edit');
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    public function onRetrieved(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    public function onCreating(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    public function onCreated(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    public function onUpdating(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    public function onUpdated(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    public function onSaving(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    public function onSaved(Model $item)
    {
    }

    /**
     * Model event ( not for soft delete )
     *
     * @param Model $item
     * @return void
     */
    public function onDeleting(Model $item)
    {
    }

    /**
     * Model event ( not for soft delete )
     *
     * @param Model $item
     * @return void
     */
    public function onDeleted(Model $item)
    {
    }

    /**
     * Model event ( for soft deletes only )
     *
     * @param Model $item
     * @return void
     */
    public function onSoftDeleting(Model $item)
    {
    }

    /**
     * Model event ( for soft deletes only )
     *
     * @param Model $item
     * @return void
     */
    public function onSoftDeleted(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    public function onRestoring(Model $item)
    {
    }

    /**
     * Model event
     *
     * @param Model $item
     * @return void
     */
    public function onRestored(Model $item)
    {
    }

    public function getGetters()
    {
        return array_merge(['tableColumns', 'searchTypes'], $this->getters);
    }

    /**
     * Widget is multilingal.
     *
     * @param boolean $val
     * @return $this
     */
    public function translation(bool $val = true)
    {
        $this->setProperty('sortable', !$val);
        $this->setProperty('translation', $val);
        return $this;
    }

    /**
     * Add required validation.
     *
     * @return $this
     */
    public function required()
    {
        $this->rules('required');
        return $this;
    }

    /**
     * Add nullable validation.
     *
     * @return $this
     */
    public function nullable()
    {
        $this->rules('nullable');
        return $this;
    }
}
