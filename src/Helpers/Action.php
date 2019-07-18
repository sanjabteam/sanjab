<?php

namespace Sanjab\Helpers;

use Illuminate\Database\Eloquent\Model;

/**
 * @method $this url (string|callable $value)           url of button.
 * @method $this target (string $value)                 action target if url used.
 * @method $this title (string $value)                  title of button.
 * @method $this icon (string $value)                   icon of button.
 * @method $this perItem (string $value)                button that just working with items.
 * @method $this variant (string $value)                variant of button, bootstrap classes like : "success", "primary", ...
 * @method $this tag (string $value)                    tag to show inside modal.
 * @method $this tagContent (string $value)             modal content inside tag.
 * @method $this tagAttributes (string $value)          modal tag attributes.
 * @method $this modalSize (string $value)              modal size. 'sm', 'md', 'lg', ...
 * @method $this action (string $value)                 ajax action.
 * @method $this confirm (string $value)                confirm message.
 * @method $this confirmYes (string $value)             confirm yes button text.
 * @method $this confirmNo (string $value)              confirm no button text.
 * @method $this confirmOk (string $value)              Ok button text after confirm action done.
 * @method $this confirmInput (string $value)           Sweetalert input for confirm type.
 * @method $this confirmInputTitle (string $value)      Sweetalert input placeholder.
 * @method $this confirmInputAttributes (string $value) Sweetalert input attributes.
 * @method $this authorize(callable)                    check allowed to call action or not.
 */
class Action extends PropertiesHolder
{
    protected $properties = [
        'icon' => 'code',
        'title' => 'TITLE HERE',
        'variant' => 'default',
        'perItem' => false,
        'tagContent' => '',
        'tagAttributes' => [],
        'modalSize' => 'md',
        'confirmInput' => null,
        'confirmInputTitle' => null,
        'confirmInputAttributes' => [],
    ];

    public function __construct(array $properties = [])
    {
        parent::__construct($properties);

        $this->confirmYes(trans('sanjab::sanjab.yes'));
        $this->confirmNo(trans('sanjab::sanjab.no'));
        $this->confirmOk(trans('sanjab::sanjab.ok'));
        $this->authorize(function () {
            return true;
        });
    }

    /**
     * create new Action button.
     *
     * @param string $title
     * @return static
     */
    public static function create($title = null)
    {
        $out = new static;
        if ($title) {
            $out->title($title);
        }

        return $out;
    }

    /**
     * Get action link.
     *
     * @property null|Model $item
     * @return string
     */
    public function getActionUrl(Model $item = null)
    {
        if (is_callable($this->property('url'))) {
            return $this->property('url')($item);
        }
        return $this->property('url');
    }
}
