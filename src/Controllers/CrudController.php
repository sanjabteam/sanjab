<?php

namespace Sanjab\Controllers;

use stdClass;
use Sanjab\Helpers\Action;
use Sanjab\Cards\StatsCard;
use Illuminate\Http\Request;
use Sanjab\Helpers\MenuItem;
use Sanjab\Helpers\SearchResult;
use Sanjab\Helpers\WidgetHandler;
use Sanjab\Helpers\CrudProperties;
use Sanjab\Helpers\PermissionItem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Base controller for CRUD controllers.
 */
abstract class CrudController extends SanjabController
{
    use WidgetHandler;

    /**
     * Array of actions.
     *
     * @var \Sanjab\Helpers\Action[]
     */
    protected $actions = [];

    /**
     * Array of actions.
     *
     * @var \Sanjab\Helpers\FilterOption[]
     */
    protected $filters = [];

    /**
     * Array of cards.
     *
     * @var \Sanjab\Cards\Card[]
     */
    protected $cards = [];

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny'.static::property('permissionsKey'), $this->property('model'));
        $this->initCrud('index');

        // items for json ajax.
        if ($request->wantsJson()) {
            $model = $this->property('model');
            $items = $model::query();
            // Do search
            if (is_array($request->input('searchTypes')) &&
                count(array_filter($request->input('searchTypes'), function ($searchType) {
                    return $searchType != null;
                })) > 0
            ) {
                // advanced search
                foreach ($this->widgets as $widget) {
                    if ($request->input('searchTypes.'.$widget->name) &&
                        $searchType = array_first(array_filter($widget->getSearchTypes(), function ($searchType) use ($request, $widget) {
                            return $searchType->type == $request->input('searchTypes.'.$widget->name);
                        }))
                    ) {
                        $items->where(function (Builder $query) use ($widget, $request, $searchType) {
                            if (count($searchType->getWidgets()) > 1) {
                                $widget->doSearch($query, $request->input('searchTypes.'.$widget->name), $request->input('search.'.$widget->name.'.'.$searchType->type));
                            } else {
                                $widget->doSearch($query, $request->input('searchTypes.'.$widget->name), array_first($request->input('search.'.$widget->name.'.'.$searchType->type)));
                            }
                        });
                    }
                }
            } else {
                // normal filter
                $this->querySearch($items, $request->input('filter'));
                if (isset($this->filters[$request->input('filterOption')])) {
                    $this->filters[$request->input('filterOption')]->property('query')($items);
                }
            }
            // Do sort
            $this->querySort($items, $request->input('sortBy'), $request->input('sortDesc') == 'true' ? true : false);
            // Customize query by query scope
            $this->queryScope($items);

            // Get and paginate
            $items = $items->paginate($request->input('perPage', $this->property('perPage')));

            // Convert each item to well formatted response including actions authorizations/urls and widget modified responses
            foreach ($items as $key => $value) {
                $items[$key] = $this->itemResponse($value);
            }

            $notification = $this->notification($request, $items);

            // Add cards data to response
            $cardsData = [];
            foreach ($this->cards as $key => $card) {
                $cardsData[$key] = new stdClass;
                $card->doModifyResponse($cardsData[$key]);
            }

            return compact('items', 'cardsData', 'notification');
        }

        // view it self without items
        return view(
            'sanjab::crud.list',
            [
                'widgets' => $this->widgets,
                'actions' => $this->actions,
                'filterOptions' => $this->filters,
                'cards' => $this->cards,
                'properties' => $this->properties(),
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create'.static::property('permissionsKey'), $this->property('model'));
        abort_unless($this->property('creatable'), 404);
        $this->initCrud('create');

        return view(
            'sanjab::crud.form',
            [
                'widgets' => $this->widgets,
                'properties' => $this->properties(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create'.static::property('permissionsKey'), $this->property('model'));
        abort_unless($this->property('creatable'), 404);
        // initialize crud
        $this->initCrud('create');
        $model = $this->property('model');
        // Create new model and save
        $item = new $model;
        $this->save($request, $item, 'create');
        // Success toast
        Session::flash('sanjab_success', trans('sanjab::sanjab.:item_created_successfully', ['item' => $this->property('title')]));

        return ['success' => true];
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = $this->property('model');
        $item = $model::where('id', $id);
        // customize query by query scope
        $this->queryScope($item);
        $item = $item->firstOrFail();
        $this->authorize('view'.static::property('permissionsKey'), $item);
        $this->initCrud('show', $item);
        // item itself if request wants json.
        $item = $this->itemResponse($item);
        if ($request->wantsJson()) {
            return $item;
        }

        return view(
            'sanjab::crud.show',
            [
                'widgets'    => $this->widgets,
                'properties' => $this->properties(),
                'item'       => $item,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_unless($this->property('editable'), 404);
        $model = $this->property('model');
        $item = $model::where('id', $id);
        // customize query by query scope
        $this->queryScope($item);
        $item = $item->firstOrFail();
        // authorization
        $this->authorize('update'.static::property('permissionsKey'), $item);
        $this->initCrud('edit', $item);
        // convert item to response object.
        $item = $this->itemResponse($item);

        return view(
            'sanjab::crud.form',
            [
                'widgets' => $this->widgets,
                'properties' => $this->properties(),
                'item' => $item,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_unless($this->property('editable'), 404);
        $model = $this->property('model');
        $item = $model::where('id', $id);
        // customize query by query scope
        $this->queryScope($item);
        $item = $item->firstOrFail();
        $this->authorize('update'.static::property('permissionsKey'), $item);
        $this->initCrud('edit', $item);
        $this->save($request, $item, 'edit');
        // success toast.
        Session::flash('sanjab_success', trans('sanjab::sanjab.:item_updated_successfully', ['item' => $this->property('title')]));

        return ['success' => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Model  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Model $item)
    {
        $item->delete();

        return ['message' => trans('sanjab::sanjab.deleted_successfully')];
    }

    /**
     * Perform action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param string $action  action name
     * @return \Illuminate\Http\Response
     */
    public function action(Request $request, $action)
    {
        $this->initCrud('action');
        // Find action in actions array
        $action = array_filter($this->actions, function ($act) use ($action) {
            return $act->action == $action;
        });
        // If did not find action then response error 404
        abort_if(count($action) == 0, 404);
        $action = array_first($action);

        // Per item action
        if ($action->perItem) {
            $model = $this->property('model');
            $items = $model::whereIn('id', $request->input('items'));
            // customize query by queryScope
            $this->queryScope($items);
            $items = $items->get();
            // Authorize all items by action
            foreach ($items as $item) {
                abort_unless($action->property('authorize')($item), 403);
            }
            // Detect action is bulk handling action or one by one model action.
            if (count(array_filter((new \ReflectionMethod(static::class, $action->action))->getParameters(), function (\ReflectionParameter $parameter) {
                return optional($parameter->getType())->getName() == 'Illuminate\Support\Collection';
            })) > 0) {
                // Call action in bulk way.
                return App::call([$this, $action->action], ['Illuminate\Support\Collection' => $items]);
            }
            // Call action per item.
            $response = null;
            foreach ($items as $item) {
                $response = App::call([$this, $action->action], [Model::class => $item, $this->property('model') => $item]);
            }

            return $response;
        }
        // Non per item action
        return App::call([$this, $action->action]);
    }

    /**
     * Get CRUD property.
     *
     * @param string $key
     * @return string|CrudProperties
     */
    final public static function property(string $key = null)
    {
        if ($key === null) {
            return static::properties();
        }

        return array_get(static::properties()->toArray(), $key);
    }

    /**
     * Properties of CRUD controller.
     *
     * @return CrudProperties
     */
    abstract protected static function properties(): CrudProperties;

    /**
     * Initialize CRUD.
     *
     * @param string $type index|show|create|edit|action
     * @param Model $item
     * @return void
     */
    final protected function initCrud(string $type, Model $item = null): void
    {
        if (isset($this->sanjabCrudInitialized) == false || $this->sanjabCrudInitialized == false) {
            $model = $this->property('model');
            $this->initWidgets($model);

            // Add create action to actions
            if ($this->property('creatable') && Auth::user()->can('create'.static::property('permissionsKey'), $this->property('model'))) {
                $this->actions[] = Action::create(trans('sanjab::sanjab.create'))
                                    ->icon('add')
                                    ->url(route('sanjab.modules.'.$this->property('route').'.create'))
                                    ->variant('success');
            }

            // Add show item action to actions
            if ($this->property('showable')) {
                $this->actions[] = Action::create(trans('sanjab::sanjab.show'))
                                    ->perItem(true)
                                    ->variant('success')
                                    ->icon('remove_red_eye')
                                    ->authorize(function ($item) {
                                        return Auth::user()->can('view'.static::property('permissionsKey'), $item);
                                    })
                                    ->url(function ($actionItem) {
                                        return route('sanjab.modules.'.$this->property('route').'.show', ['id' => $actionItem->id]);
                                    });
            }

            // Add edit item action to actions
            if ($this->property('editable')) {
                $this->actions[] = Action::create(trans('sanjab::sanjab.edit'))
                                    ->perItem(true)
                                    ->variant('warning')
                                    ->icon('edit')
                                    ->authorize(function ($item) {
                                        return Auth::user()->can('update'.static::property('permissionsKey'), $item);
                                    })
                                    ->url(function ($actionItem) {
                                        return route('sanjab.modules.'.$this->property('route').'.edit', ['id' => $actionItem->id]);
                                    });
            }

            // Add delete item action to actions
            if ($this->property('deletable')) {
                $this->actions[] = Action::create(trans('sanjab::sanjab.delete'))
                                    ->perItem(true)
                                    ->action('destroy')
                                    ->variant('danger')
                                    ->icon('delete')
                                    ->confirm(trans('sanjab::sanjab.are_you_sure_you_want_to_delete?'))
                                    ->authorize(function ($item) {
                                        return Auth::user()->can('delete'.static::property('permissionsKey'), $item);
                                    });
            }

            // Add default stats card (simple counter) to cards list
            if ($this->property('defaultCards')) {
                $itemsCount = $model::query();
                static::queryScope($itemsCount);
                $this->cards[] = StatsCard::create(static::property('titles'))
                                    ->value($itemsCount->count())
                                    ->icon(static::property('icon'));
            }

            // Call user defined init
            $this->init($type, $item);
            // Call widgets post init
            $this->postInitWidgets($type, $item);
            // Call cards post init
            foreach ($this->cards as $card) {
                $card->postInit();
            }
            // Sort cards
            usort($this->cards, function ($a, $b) {
                return $a->order > $b->order;
            });
            $this->sanjabCrudInitialized = true;
        }
    }

    /**
     * Using to override initialize.
     *
     * @param string $type
     * @param Model $item
     * @return void
     */
    abstract protected function init(string $type, Model $item = null): void;

    /**
     * Modify query before access it.
     *
     * @param Builder $query
     * @return void
     */
    protected function queryScope(Builder $query)
    {
    }

    /**
     * Should play notification sound or not.
     *
     * @param Request $request
     * @param LengthAwarePaginator $items
     * @return bool|string
     */
    protected function notification(Request $request, LengthAwarePaginator $items)
    {
        return false;
    }

    /**
     * Perform search query by widgets.
     *
     * @param Builder $query
     * @param string|null $search
     * @return void
     */
    final protected function querySearch(Builder $query, $search)
    {
        if (! empty($search)) {
            $query->where(function ($query) use ($search) {
                // Non translated widgets search.
                foreach ($this->widgets as $widget) {
                    if (! $widget->property('translation')) {
                        $query->orWhere(function ($query) use ($widget, $search) {
                            $widget->doSearch($query, null, $search);
                        });
                    }
                }

                // translated widgets search
                // first check any translated widget exists or not
                if (count(array_filter($this->widgets, function ($widget) {
                    return $widget->property('translation');
                })) > 0) {
                    $query->orWhereHas('translations', function (Builder $query) use ($search) {
                        $query->where(function (Builder $query) use ($search) {
                            foreach ($this->widgets as $widget) {
                                if ($widget->property('translation')) {
                                    $query->orWhere(function ($query) use ($widget, $search) {
                                        $widget->doSearch($query, null, $search);
                                    });
                                }
                            }
                        });
                    });
                }
            });
        }
    }

    /**
     * Perform sort query by widgets.
     *
     * @param Builder $query
     * @param string|null $sort  sort column
     * @param bool $sortDesc  sort desc.
     * @return void
     */
    final protected function querySort(Builder $query, $sort, bool $sortDesc)
    {
        // perform sort by widget field if sort is not empty.
        if (! empty($sort)) {
            foreach ($this->widgets as $widget) {
                foreach ($widget->getTableColumns() as $tableColumn) {
                    if ($sort == $tableColumn->key) {
                        $widget->doOrder(
                            $query,
                            $sort,
                            $sortDesc ? 'desc' : 'asc'
                        );
                    }
                }
            }
        } else {
            // order by default
            $query->orderBy(str_replace('__TABLE__', app($this->property('model'))->getTable(), $this->property('defaultOrder')), $this->property('defaultOrderDirection'));
        }
    }

    public static function routes(): void
    {
        Route::prefix('modules')->name('modules.')->group(function () {
            Route::post(static::property('route').'/action/{action}', static::class.'@action')->name(static::property('route').'action');
            Route::resource(static::property('route'), static::class)
                            ->parameters([
                                static::property('route') => 'id',
                            ])
                            ->except(['destroy']);
        });
    }

    public static function menus(): array
    {
        $menu = [
            MenuItem::create(route('sanjab.modules.'.static::property('route').'.index'))
                    ->title(static::property('titles'))
                    ->icon(static::property('icon'))
                    ->badge(static::property('badge'))
                    ->badgeVariant(static::property('badgeVariant'))
                    ->active(function () {
                        return Route::is('sanjab.modules.'.static::property('route').'.*');
                    })
                    ->hidden(function () {
                        return Auth::user()->cannot('viewAny'.static::property('permissionsKey'), static::property('model'));
                    }),
        ];
        if (static::property('menuParentText')) {
            return [
                MenuItem::create('javascript:void(0);')
                    ->title(static::property('menuParentText'))
                    ->icon(static::property('menuParentIcon'))
                    ->addChildren($menu),
            ];
        }

        return $menu;
    }

    public static function permissions(): array
    {
        $permission = PermissionItem::create(static::property('titles'))
                        ->addPermission(trans('sanjab::sanjab.show_:item', ['item' => static::property('titles')]), 'viewAny'.static::property('permissionsKey'), static::property('model'));
        if (static::property('showable')) {
            $permission->addPermission(trans('sanjab::sanjab.show_:item', ['item' => static::property('title')]), 'view'.static::property('permissionsKey'), static::property('model'));
        }
        if (static::property('creatable')) {
            $permission->addPermission(trans('sanjab::sanjab.create_:item', ['item' => static::property('title')]), 'create'.static::property('permissionsKey'), static::property('model'));
        }
        if (static::property('editable')) {
            $permission->addPermission(trans('sanjab::sanjab.edit_:item', ['item' => static::property('title')]), 'update'.static::property('permissionsKey'), static::property('model'));
        }
        if (static::property('deletable')) {
            $permission->addPermission(trans('sanjab::sanjab.delete_:item', ['item' => static::property('title')]), 'delete'.static::property('permissionsKey'), static::property('model'));
        }

        return [$permission];
    }

    public static function dashboardCards(): array
    {
        if (static::property('defaultDashboardCards') && Auth::user()->can('viewAny'.static::property('permissionsKey'), static::property('model'))) {
            $model = static::property('model');
            $items = $model::query();
            app(static::class)->queryScope($items);

            return [
                StatsCard::create(static::property('titles'))
                            ->value($items->count())
                            ->link(route('sanjab.modules.'.static::property('route').'.index'))
                            ->variant(array_random(['primary', 'secondary', 'success', 'info', 'warning', 'danger']))
                            ->icon(static::property('icon')),
            ];
        }

        return [];
    }

    public static function globalSearch(string $search): array
    {
        $results = [];
        // Authorize can user access to items list atleasts.
        if (Auth::user()->can('viewAny'.static::property('permissionsKey'), static::property('model')) && static::property('globalSearch')) {
            $controllerInsatance = app(static::class);
            App::call([$controllerInsatance, 'index']);
            $model = static::property('model');
            $items = $model::query();
            // Search scope and query scope
            $controllerInsatance->querySearch($items, $search);
            $controllerInsatance->queryScope($items);
            $items = $items->get();

            // Check controller title is searched or not
            if (preg_match('/.*'.preg_quote($search).'.*/', static::property('title')) || preg_match('/.*'.preg_quote($search).'.*/', static::property('titles'))) {
                $results[] = SearchResult::create(static::property('titles'), route('sanjab.modules.'.static::property('route').'.index'))
                                            ->icon(static::property('icon'))
                                            ->order(50);
            }

            // Item format. if it's null then name field or title field will be used.
            $itemFormat = null;
            if (static::property('itemFormat')) {
                $itemFormat = static::property('itemFormat');
            } elseif (! empty(optional($model::first())->name)) {
                $itemFormat = '%name';
            } elseif (! empty(optional($model::first())->title)) {
                $itemFormat = '%title';
            }
            foreach ($items as $item) {
                $itemName = $itemFormat;
                preg_match_all('/%([A-Za-z0-9_]+)/', $itemFormat, $matches);
                // Format item by requested format
                foreach ($matches[1] as $match) {
                    $itemName = str_replace('%'.$match, $item->{ $match }, $itemName);
                }
                // Check searched string exists in formated output.
                if (preg_match('/.*'.preg_quote($search).'.*/', $itemName)) {
                    if (static::property('editable') && Auth::user()->can('edit'.static::property('permissionsKey'), $item)) {
                        // Link to item's edit if user can edit
                        $results[] = SearchResult::create(trans('sanjab::sanjab.:item_in_:part', ['item' => $itemName, 'part' => static::property('titles')]), route('sanjab.modules.'.static::property('route').'.edit', ['id' => $item->id]))
                                                    ->icon(static::property('icon'));
                    } elseif (static::property('showable') && Auth::user()->can('view'.static::property('permissionsKey'), $item)) {
                        // Link to item's view if user can view
                        $results[] = SearchResult::create(trans('sanjab::sanjab.:item_in_:part', ['item' => $itemName, 'part' => static::property('titles')]), route('sanjab.modules.'.static::property('route').'.show', ['id' => $item->id]))
                                                    ->icon(static::property('icon'));
                    } else {
                        // Link to list
                        $results[] = SearchResult::create(trans('sanjab::sanjab.:item_in_:part', ['item' => $itemName, 'part' => static::property('titles')]), route('sanjab.modules.'.static::property('route').'.index'))
                                                    ->icon(static::property('icon'));
                    }
                }
            }
        }

        return $results;
    }
}
