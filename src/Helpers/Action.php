<?php

namespace Sanjab\Helpers;

use Illuminate\Database\Eloquent\Model;

/**
 * @method $this target (string $value)                 action target if url used.
 * @method $this title (string $value)                  title of button.
 * @method $this icon (string $value)                   icon of button.
 * @method $this perItem (string $value)                button that just working with items.
 * @method $this bulk (bool $value)                     can be used with multiple.
 * @method $this variant (string $value)                variant of button, bootstrap classes like : "success", "primary", ...
 * @method $this tag (string $value)                    tag to show inside modal.
 * @method $this tagContent (string $value)             modal content inside tag.
 * @method $this tagAttributes (array $value)           modal tag attributes.
 * @method $this modalSize (string $value)              modal size. 'sm', 'md', 'lg', ...
 * @method $this action (string $value)                 ajax action.
 * @method $this confirm (string $value)                confirm message.
 * @method $this confirmYes (string $value)             confirm yes button text.
 * @method $this confirmNo (string $value)              confirm no button text.
 * @method $this confirmOk (string $value)              Ok button text after confirm action done.
 * @method $this confirmInput (string $value)           Sweetalert input for confirm type.
 * @method $this confirmInputTitle (string $value)      Sweetalert input placeholder.
 * @method $this confirmInputAttributes (array $value)  Sweetalert input attributes.
 * @method $this authorize(callable)                    check allowed to call action or not.
 */
class Action extends PropertiesHolder
{
    protected $properties = [
        'icon' => 'code',
        'title' => 'TITLE HERE',
        'variant' => 'default',
        'perItem' => false,
        'bulk' => true,
        'bulkUrl' => false,
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
     * URL of button.
     *
     * @param string|callable $value
     * @return $this
     */
    public function url($value)
    {
        $this->setProperty('url', $value);
        $this->bulk(false);
        if (is_callable($value) && count(array_filter((new \ReflectionFunction($value))->getParameters(), function (\ReflectionParameter $parameter) {
            return optional($parameter->getType())->getName() == 'Illuminate\Support\Collection';
        })) > 0) {
            $this->setProperty('bulkUrl', true);
        }
        return $this;
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
            if ($this->property('bulkUrl')) {
                $result = $this->property('url')(collect([$item]));
                if (is_array($result)) {
                    return array_first($result);
                }
                if ($result instanceof \Illuminate\Support\Collection) {
                    return $result->first();
                }
                return $result;
            }

            return $this->property('url')($item);
        }

        return $this->property('url');
    }
}
