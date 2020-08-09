<?php

namespace Sanjab\Helpers;

/**
 * @method $this url (string $value)       search result URL.
 * @method $this title (string $value)     search result title.
 * @method $this icon (string $value)      search result icon.
 * @method $this order (int $value)        search result order.
 */
class SearchResult extends PropertiesHolder
{
    protected $properties = [
        'icon' => 'search',
        'title' => 'TITLE HERE',
        'order' => 100,
    ];

    /**
     * create new Search result.
     *
     * @param  null  $title
     * @param  null  $url
     *
     * @return static
     * @property string $url    url of search result.
     * @property string $title  title of search result.
     */
    public static function create($title = null, $url = null)
    {
        $out = new static;
        if ($title) {
            $out->title($title);
        }
        if ($url) {
            $out->url($url);
        }

        return $out;
    }
}
