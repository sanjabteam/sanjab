<?php

namespace Sanjab\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Sanjab\Helpers\InteractsWithWidget;

class CheckboxWidgetController extends SanjabController
{
    use InteractsWithWidget;

    public function change(Request $request)
    {
        $this->validate($request, [
            'value'      => 'required|boolean',
        ]);
        [$controllerInsatance, $checkboxWidget] = $this->getInteractionInfo();

        $model = $controllerInsatance->property('model');
        if (! class_exists($model)) { // check model exist
            return abort(400);
        }

        $item = $model::where('id', $request->input('item'))->withoutGlobalScopes()->firstOrFail();
        if (! $checkboxWidget->property("fastChangeControllerAuthorize")($item)) { // check user authorized to update model
            return response()->json(['success' => false], 403);
        }

        $item->{$checkboxWidget->name} = $request->input('value') == true;

        if ($checkboxWidget->property('fastChangeTimestamps') == false) { // should we update updated_at field
            $item->timestamps = false;
        }

        ($checkboxWidget->property('fastChangeBefore'))($item);
        $item->save();
        ($checkboxWidget->property('fastChangeAfter'))($item);
        $item->save();
        return ['success' => true];
    }

    public static function routes(): void
    {
        Route::post('/helpers/checkbox-widget/change', static::class.'@change')->name('helpers.checkbox-widget.change');
    }
}
