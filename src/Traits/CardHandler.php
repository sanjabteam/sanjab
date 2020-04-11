<?php

namespace Sanjab\Traits;

use stdClass;
use Illuminate\Database\Eloquent\Model;

trait CardHandler
{
    /**
     * Array of cards.
     *
     * @var \Sanjab\Cards\Card[]
     */
    protected $cards = [];

    /**
     * Post initialize after card ready.
     *
     * @param string $type
     * @param Model|null $item
     * @return void
     */
    public function postInitCards(string $type, Model $item = null)
    {
        foreach ($this->cards as $key => $card) {
            $this->cards[$key]->controllerProperties = $this->properties()->toArray();
            $this->cards[$key]->controllerProperties['controller'] = static::class;
            $this->cards[$key]->controllerProperties['type'] = $type;
            $this->cards[$key]->controllerProperties['item'] = $item;
            $this->cards[$key]->controllerProperties['index'] = $key;
            $this->cards[$key]->postInit();
        }
    }

    /**
     * Get data from cards as array.
     *
     * @return array
     */
    public function getCardsData()
    {
        $cardsData = [];
        foreach ($this->cards as $key => $card) {
            $cardsData[$key] = new stdClass;
            $card->doModifyResponse($cardsData[$key]);
        }
        return $cardsData;
    }

    /**
     * Sort cards based on order property.
     *
     * @return void
     */
    public function sortCards()
    {
        usort($this->cards, function ($a, $b) {
            return $a->order > $b->order;
        });
    }

    /**
     * Cards getter.
     *
     * @return array
     */
    public function getCards()
    {
        return $this->cards;
    }
}
