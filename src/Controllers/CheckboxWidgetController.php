<?php

namespace Sanjab\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Sanjab\Traits\InteractsWithWidget;

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
        // check model exist
        if (! class_exists($model)) {
            return abort(400);
        }

        // Get item.
        $item = $model::where('id', $request->input('item'))->withoutGlobalScopes()->firstOrFail();
        // Check user authorized to update model
        if (! $checkboxWidget->property('fastChangeControllerAuthorize')($item)) {
            return response()->json(['success' => false], 403);
        }

        $item->{$checkboxWidget->name} = $request->input('value') == true;

        // should we update updated_at field or not
        if ($checkboxWidget->property('fastChangeTimestamps') == false) {
            $item->timestamps = false;
        }

        // Call before save callback
        ($checkboxWidget->property('fastChangeBefore'))($item);
        $item->save();
        // Call after save callback
        ($checkboxWidget->property('fastChangeAfter'))($item);
        $item->save();

        return ['success' => true];
    }

    public static function routes(): void
    {
        Route::post('/helpers/checkbox-widget/change', static::class.'@change')->name('helpers.checkbox-widget.change');
    }
}
