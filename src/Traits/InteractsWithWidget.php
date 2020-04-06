<?php

namespace Sanjab\Traits;

use Illuminate\Support\Facades\App;

/**
 * To get informations about a widget from an external controller.
 */
trait InteractsWithWidget
{
    /**
     * Get widget interaction values.
     *
     * @return array
     */
    protected function getInteractionInfo()
    {
        $request = request();
        $this->validate($request, [
            'controller' => 'required|string',
            'action'     => 'required|string',
            'item'       => 'nullable|numeric',
            'widget'     => 'required|string',
        ]);
        $controller = $request->input('controller');
        if (! class_exists($controller)) {
            return abort(400, 'Controller is not valid.');
        }
        if (! method_exists($controller, $request->input('action'))) {
            return abort(400, 'Controller action is not valid.');
        }
        $controllerInsatance = app($request->input('controller'));
        $controllerAction = $request->input('action');
        $controllerActionParameters = [];
        if ($request->filled('item')) {
            $controllerActionParameters[] = $request->input('item');
        }
        App::call([$controllerInsatance, $controllerAction], $controllerActionParameters);

        $outWidget = null;
        foreach ($controllerInsatance->getWidgets() as $widget) {
            if ($widget->name == $request->input('widget')) {
                $outWidget = $widget;
            }
        }
        if ($outWidget == null) {
            return abort(400, 'Widget '.$request->input('widget').' is not valid.');
        }

        return [$controllerInsatance, $outWidget];
    }
}
