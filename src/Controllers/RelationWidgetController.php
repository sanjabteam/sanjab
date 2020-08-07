<?php

namespace Sanjab\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Sanjab\Traits\InteractsWithWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class RelationWidgetController extends SanjabController
{
    use InteractsWithWidget;

    public function options(Request $request)
    {
        $this->validate($request, [
            'search'     => 'nullable|string',
            'selected'   => 'nullable|array',
            'selected.*' => 'numeric',
        ]);

        if (session('sanjab_relation_widget_'.$request->input('controller').'_'.$request->input('widget')) == null) {
            throw ValidationException::withMessages(['widget' => 'Widget is invalid']);
        }

        $relationWidget = unserialize(session('sanjab_relation_widget_'.$request->input('controller').'_'.$request->input('widget')));
        $model = $relationWidget->relatedModel;

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
                            $query->where($query->getQuery()->from.'.'.$field, 'LIKE', '%'.$request->input('search').'%');
                        });
                    } else {
                        $query->orWhere($query->getQuery()->from.'.'.$searchField, 'LIKE', '%'.$request->input('search').'%');
                    }
                }
            }
            if (is_array($request->input('selected')) && count($request->input('selected')) > 0) {
                $query->orWhereIn('id', $request->input('selected'));
            }
        });
        if ($request->filled('search') == false && (is_array($request->input('selected')) == false || count($request->input('selected')) == 0)) {
            $items->inRandomOrder();
        }
        $items = $items->get();
        $out = [];
        $format = $relationWidget->format;
        $matches = [[], []];
        if (is_string($format)) {
            preg_match_all('/%([A-Za-z0-9_]+)/', $format, $matches);
        }
        foreach ($items as $item) {
            $text = null;
            if (is_callable($format)) {
                $text = $format($item);
            } else {
                $text = $format;
                foreach ($matches[1] as $match) {
                    $text = str_replace('%'.$match, $item->{ $match }, $text);
                }
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
