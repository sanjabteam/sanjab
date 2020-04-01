<?php

namespace Sanjab\Helpers;

use JsonSerializable;
use Illuminate\Contracts\Support\Arrayable;

class PropertiesHolder implements Arrayable, JsonSerializable
{
    /**
     * List of properties.
     *
     * @var array
     */
    protected $properties = [];

    /**
     * List of getter functions that should be present in json response.
     *
     * @var array
     */
    protected $getters = [];

    /**
     * List of properties that should be hidden from json response.
     *
     * @var array
     */
    protected $hidden = [];

    public function __construct(array $properties = [])
    {
        $this->properties = array_merge($this->properties, $properties);
    }

    public function __call($method, $arguments)
    {
        if (count($arguments) == 1) {
            $this->properties[$method] = array_first($arguments);

            return $this;
        }

        return $this;
    }

    public function __get($name)
    {
        if (method_exists($this, 'get'.str_replace(['-', '_'], '', title_case($name)))) {
            return call_user_func_array([$this, 'get'.str_replace(['-', '_'], '', title_case($name))], []);
        }
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }
    }

    /**
     * Getters.
     *
     * @return array
     */
    public function getGetters()
    {
        return $this->getters;
    }

    public function toArray()
    {
        $out = $this->properties;
        foreach ($this->getGetters() as $getter) {
            $out[$getter] = $this->__get($getter);
        }

        return array_filter($out, function ($property, $key) {
            return ! in_array($key, $this->hidden);
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get property.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function property(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->properties;
        }

        return array_get($this->properties, $key, $default);
    }

    /**
     * Set property.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setProperty(string $key, $value)
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * create new Properties Holder.
     *
     * @return static
     */
    public static function create()
    {
        $out = new static;

        return $out;
    }
}
