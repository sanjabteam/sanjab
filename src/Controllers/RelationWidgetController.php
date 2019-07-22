<?php

namespace Sanjab\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Sanjab\Helpers\InteractsWithWidget;

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
        $relationWidget = $this->getInteractionInfo()[1];
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
