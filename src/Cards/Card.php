<?php

namespace Sanjab\Cards;

use stdClass;
use Sanjab\Helpers\PropertiesHolder;

/**
 * Base class for all cards.
 *
 * @method $this tag(string $val)           tag of card.
 * @method $this cols(int $val)             bootstrap based column width.
 * @method $this title(string $val)         title of card.
 * @method $this order(int $val)            order of card.
 */
abstract class Card extends PropertiesHolder
{
    public function __construct(array $properties = [])
    {
        $this->tag("input")->cols(12)->order(100);
        parent::__construct($properties);
        $this->init();
    }

    /**
     * create new card.
     *
     * @return static
     */
    final public static function create($title = null)
    {
        $out = new static();
        if ($title) {
            $out->title($title);
        }
        return $out;
    }

    /**
     * Called when card created.
     *
     * @return void
     */
    abstract public function init();

    /**
     * Called when all cards has been created.
     *
     * @return void
     */
    public function postInit()
    {
    }

    /**
     * Do modifying response.
     *
     * @param object $response
     * @return void
     */
    final public function doModifyResponse(stdClass $response)
    {
        $this->modifyResponse($response);
    }

    /**
     * To override modifying card data.
     *
     * @param object $respones
     * @return void
     */
    protected function modifyResponse(stdClass $response)
    {
    }
}
