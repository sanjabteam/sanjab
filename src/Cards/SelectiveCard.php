<?php

namespace Sanjab\Cards;

/**
 * Collection of cards with a select box to change.
 *
 * @method $this selectiveController(string $val)       Controller to load selective cards data.
 * @method $this selectiveControllerAction(string $val) Controller action to load selective cards data.
 * @method $this selectiveControllerItem(string $val)   Controller action parameter to load selective cards data.
 * @method $this selectiveControllerIndex(int $val)     Selective card index in cards list.
 * @method $this inline(bool $val)                      Should be Title and selectbox overlay on card.
 */
class SelectiveCard extends Card
{
    /**
     * All cards.
     *
     * @var array
     */
    protected $cards = [];

    /**
     * List of getter functions that should be present in json response.
     *
     * @var array
     */
    protected $getters = [
        'cards',
        'controller',
        'controllerAction',
        'controllerItem',
        'controllerIndex',
    ];

    public function init()
    {
        $this->tag('selective-card');
    }

    /**
     * Add a card to collection.
     *
     * @param string $title
     * @param Card $card
     * @return $this
     */
    public function addCard(string $title, Card $card)
    {
        $this->cards[] = ['title' => $title, 'card' => $card];

        return $this;
    }

    /**
     * Add multiple card to collection..
     *
     * @param array $cards  array of cards with title as key.
     * @return $this
     */
    public function addCards(array $cards)
    {
        foreach ($cards as $title => $card) {
            $cards[] = ['title' => $title, 'card' => $card];
        }

        return $this;
    }

    /**
     * Get cards.
     *
     * @return array
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * Selective data controller getter.
     *
     * @return string
     */
    public function getController()
    {
        if (isset($this->controllerProperties['controller']) == false && empty($this->property('selectiveController'))) {
            throw new \Exception("Please set selective controller for '".$this->property('name')."'");
        }
        if (! empty($this->property('selectiveController'))) {
            return $this->property('selectiveController');
        }

        return $this->controllerProperties['controller'];
    }

    /**
     * Selective data controller action getter.
     *
     * @return string
     */
    public function getControllerAction()
    {
        if (isset($this->controllerProperties['type']) == false && empty($this->property('selectiveControllerAction'))) {
            throw new \Exception("Please set selective controller action for '".$this->property('name')."'");
        }
        if (! empty($this->property('selectiveControllerAction'))) {
            return $this->property('selectiveControllerAction');
        }

        return $this->controllerProperties['type'];
    }

    /**
     * Selective data controller card index getter.
     *
     * @return string
     */
    public function getControllerIndex()
    {
        if (isset($this->controllerProperties['index']) == false && empty($this->property('selectiveControllerIndex'))) {
            throw new \Exception("Please set selective controller action for '".$this->property('name')."'");
        }
        if (! empty($this->property('selectiveControllerIndex'))) {
            return $this->property('selectiveControllerIndex');
        }

        return $this->controllerProperties['index'];
    }

    /**
     * Selective data controller item getter.
     *
     * @return mixed
     */
    public function getControllerItem()
    {
        if (isset($this->controllerProperties['item'])) {
            return optional($this->controllerProperties['item'])->id;
        }

        return optional($this->property('selectiveControllerItem'))->id;
    }
}
