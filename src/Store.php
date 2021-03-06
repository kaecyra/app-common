<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon;

use Psr\Container\ContainerInterface;

/**
 * Centralized data mart
 *
 * Allows data persist even through reloads.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
class Store implements ContainerInterface {

    /**
     * Data store
     * @var array
     */
    public $data;

    public function __construct() {
        $this->flush();
    }

    /**
     * Write data to store
     *
     * @param array $data
     */
    public function prepare($data) {
        $this->data = $data;
    }

    /**
     * Merge data into store
     *
     * @param array $data
     */
    public function merge($data) {
        $this->data = array_replace_recursive($this->data, $data);
    }

    /**
     * Get data from store
     *
     * @return array
     */
    public function dump() {
        return $this->data;
    }

    /**
     * Erase all stored data
     *
     */
    public function flush() {
        $this->data = [];
    }

    /**
     * Do we have data?
     *
     * @return boolean
     */
    public function hasData(): bool {
        return count($this->data);
    }

    /**
     * Get a store variable
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        return valr(trim($key), $this->data, $default);
    }

    /**
     * Set a store value
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set($key, $value) {
        setvalr(trim($key), $this->data, $value);
        return $value;
    }

    /**
     * Check if key exists
     *
     * @param string $key
     */
    public function has($key): bool {
        $key = trim($key);
        $path = explode('.', $key);
        $pathLength = count($path);
        $target = &$this->data;
        for ($i = 1; $i <= $pathLength; ++$i) {
            $subKey = $path[$i - 1];

            // no such key!
            if (!isset($target[$subKey]) || ($i < $pathLength && !is_array($target[$subKey]))) {
                return false;
            }

            if ($i < $pathLength) {
                $target = &$target[$subKey];
            }
        }
        return true;
    }

    /**
     * Unset a key
     *
     * @param string $key
     * @return boolean unset success or failure
     */
    public function delete($key): bool {
        $key = trim($key);
        $path = explode('.', $key);
        $pathLength = count($path);
        $target = &$this->data;
        for ($i = 1; $i <= $pathLength; ++$i) {
            $subKey = $path[$i - 1];

            // no such key!
            if (!isset($target[$subKey]) || ($i < $pathLength && !is_array($target[$subKey]))) {
                return false;
            }

            if ($i < $pathLength) {
                $target = &$target[$subKey];
            }
        }
        unset($target[$subKey]);
        return true;
    }

    /**
     * Push data onto key
     *
     * @param string $key
     * @param array $data
     */
    public function push($key, $data) {
        $key = trim($key);
        if (!array_key_exists($key, $this->data) || !is_array($this->data[$key])) {
            $this->data[$key] = [];
        }
        array_push($this->data[$key], $data);
    }

}
