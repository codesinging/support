<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2020/1/6 09:43
 */

namespace CodeSinging\Support;

class Configurable implements \ArrayAccess
{
    /**
     * All of the configurations set on the container.
     *
     * @var array
     */
    protected $configs = [];

    /**
     * Create a new fluent container instance.
     *
     * @param array $configs
     *
     * @return void
     */
    public function __construct(array $configs = [])
    {
        $this->set($configs);
    }

    /**
     * Set a configuration to the container.
     *
     * @param string|array $key
     * @param null         $value
     *
     * @return $this
     */
    public function set($key, $value = null)
    {
        if (is_string($key)) {
            $this->configs[$key] = $value;
        } elseif (is_array($key)) {
            $this->configs = array_merge($this->configs, $key);
        }
        return $this;
    }

    /**
     * Get a configuration from the container.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->configs)) {
            return $this->configs[$key];
        }

        return $default;
    }

    /**
     * Get the configurations from the container.
     *
     * @return array
     */
    public function configs()
    {
        return $this->configs;
    }

    /**
     * Handle dynamic calls to the container to set configurations.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $this->configs[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * Convert the Fluent instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->configs;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the Fluent instance to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Determine if the given offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->configs[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set the value at the given offset.
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->configs[$offset] = $value;
    }

    /**
     * Unset the value at the given offset.
     *
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->configs[$offset]);
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param string $key
     *
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }
}