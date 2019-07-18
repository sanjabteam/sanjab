<?php

namespace Sanjab\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

class RelationWidgetController extends SanjabController
{
    public function options(Request $request)
    {
        $this->validate($request, [
            'search'     => 'nullable|string',
            'selected'   => 'nullable|array',
            'selected.*' => 'numeric',
            'controller' => 'required|string',
            'action'     => 'required|string',
            'item'       => 'nullable|numeric',
            'name'       => 'required|string',
        ]);
        $controller = $request->input("controller");
        if (! class_exists($controller)) {
            return abort(400, "Controller is not valid.");
        }
        $controllerInsatance = app($request->input('controller'));
        $controllerAction = $request->input('action');
        $controllerActionParameters = [];
        if ($request->filled('item')) {
            $controllerActionParameters[] = $request->input('item');
        }
        App::call([$controllerInsatance, $controllerAction], $controllerActionParameters);
        $relationWidget = null;
        foreach ($controllerInsatance->getWidgets() as $widget) {
            if ($widget->name == $request->input('name')) {
                $relationWidget = $widget;
            }
        }
        if ($relationWidget == null) {
            return abort(400, "Widget name is not valid.");
        }
        $model = $relationWidget->model;
        $format = $relationWidget->format;

        preg_match_all("/%([A-Za-z0-9_]+)/", $format, $matches);

        $items = $model::limit(100);
        $items->where(function ($query) use ($relationWidget) {
            ($relationWidget->query)($query);
        });
        $items->where(function ($query) use ($request, $relationWidget) {
            if ($request->filled('search')) {
                foreach ($relationWidget->searchFields as $searchField) {
                    if (count(explode('.', $searchField)) > 1) {
                        $relation = preg_replace('/\.[A-Za-z0-9_]+$/', '', $searchField);
                        $field = str_replace($relation.'.', '', $searchField);
                        $query->orWhereHas($relation, function (Builder $query) use ($field, $request) {
                            $query->where($query->getQuery()->from.'.'.$field, "LIKE", "%".$request->input('search')."%");
                        });
                    } else {
                        $query->orWhere($query->getQuery()->from.'.'.$searchField, "LIKE", "%".$request->input('search')."%");
                    }
                }
            }
            if (is_array($request->input('selected'))) {
                $query->orWhereIn('id', $request->input('selected'));
            }
        });
        $items = $items->get();
        $out = [];
        foreach ($items as $item) {
            $text = $format;
            foreach ($matches[1] as $match) {
                $text = str_replace("%".$match, $item->{ $match }, $text);
            }
            $out[] = ['label' => $text, 'value' => $item->id];
        }
        return $out;
    }

    public static function routes(): void
    {
        Route::post('/helpers/relation-widgets/options', static::class.'@options')->name('helpers.relation-widgets.options');
    }
}
