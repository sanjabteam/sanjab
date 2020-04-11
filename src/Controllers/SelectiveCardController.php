<?php

namespace Sanjab\Controllers;

use Illuminate\Http\Request;
use Sanjab\Traits\InteractsWithCard;
use Illuminate\Support\Facades\Route;
use stdClass;

class SelectiveCardController extends SanjabController
{
    use InteractsWithCard;

    /**
     * Get data for card inside selective card.
     *
     * @param Request $request
     * @return void
     */
    public function data(Request $request)
    {
        [$controllerInsatance, $selectiveCard] = $this->getInteractionInfo();
        $request->validate(['dataIndex' => 'required']);
        $cards = $selectiveCard->getCards();
        if (! isset($cards[$request->input('dataIndex')])) {
            return abort(400, 'Card data index is invalide.');
        }
        $card = $cards[$request->input('dataIndex')];
        if (! isset($card['card'])) {
            return abort(400, 'Selective card misconfigured.');
        }
        $cardData = new stdClass;
        $card['card']->doModifyResponse($cardData);
        return (array)$cardData;
    }

    public static function routes(): void
    {
        Route::post('/helpers/selective-card/data', static::class.'@data')->name('helpers.selective-card.data');
    }
}
