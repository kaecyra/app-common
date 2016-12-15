<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

/**
 * General functions
 *
 * @package app-common
 */

/**
 * Concatenate path elements into single string.
 *
 * Takes a variable number of arguments and concatenates them. Delimiters will
 * not be duplicated. Example: all of the following invocations will generate
 * the path "/path/to/vanilla/applications/dashboard"
 *
 * '/path/to/vanilla', 'applications/dashboard'
 * '/path/to/vanilla/', '/applications/dashboard'
 * '/path', 'to', 'vanilla', 'applications', 'dashboard'
 * '/path/', '/to/', '/vanilla/', '/applications/', '/dashboard'
 *
 * @return string Returns the concatenated path.
 */
function paths() {
    $paths = func_get_args();
    $delimiter = '/';
    if (is_array($paths)) {
        $mungedPath = implode($delimiter, $paths);
        $mungedPath = str_replace(
            array($delimiter.$delimiter.$delimiter, $delimiter.$delimiter),
            array($delimiter, $delimiter),
            $mungedPath
        );
        return str_replace(array('http:/', 'https:/'), array('http://', 'https://'), $mungedPath);
    } else {
        return $paths;
    }
}

/**
 * Return the value from an associative array or an object.
 *
 * @param string $key The key or property name of the value.
 * @param mixed $collection The array or object to search.
 * @param mixed $default The value to return if the key does not exist.
 * @return mixed The value from the array or object.
 */
function val($key, $collection, $default = false) {
    if (is_array($collection)) {
        if (array_key_exists($key, $collection)) {
            return $collection[$key];
        } else {
            return $default;
        }
    } elseif (is_object($collection) && property_exists($collection, $key)) {
        return $collection->$key;
    }
    return $default;
}

/**
 * Return the value from an associative array or an object.
 *
 * This function differs from GetValue() in that $Key can be a string consisting of dot notation that will be used
 * to recursively traverse the collection.
 *
 * @param string $key The key or property name of the value.
 * @param mixed $collection The array or object to search.
 * @param mixed $default The value to return if the key does not exist.
 * @return mixed The value from the array or object.
 */
function valr($key, $collection, $default = false) {
    $path = explode('.', $key);

    $value = $collection;
    for ($i = 0; $i < count($path); ++$i) {
        $subKey = $path[$i];

        if (is_array($value) && isset($value[$subKey])) {
            $value = $value[$subKey];
        } elseif (is_object($value) && isset($value->$subKey)) {
            $value = $value->$subKey;
        } else {
            return $default;
        }
    }
    return $value;
}

/**
 * Set a key to a value in a collection.
 *
 * Works with single keys or "dot" notation. If $key is an array, a simple
 * shallow array_merge is performed.
 *
 * @param string $key The key or property name of the value.
 * @param array &$collection The array or object to search.
 * @param mixed $value The value to set.
 * @return mixed Newly set value or if array merge.
 */
function setvalr($key, &$collection, $value = null) {
    if (is_array($key)) {
        $collection = array_merge($collection, $key);
        return null;
    }

    if (strpos($key, '.')) {
        $path = explode('.', $key);

        $selection = &$collection;
        $mx = count($path) - 1;
        for ($i = 0; $i <= $mx; ++$i) {
            $subSelector = $path[$i];

            if (is_array($selection)) {
                if (!isset($selection[$subSelector])) {
                    $selection[$subSelector] = array();
                }
                $selection = &$selection[$subSelector];
            } elseif (is_object($selection)) {
                if (!isset($selection->$subSelector)) {
                    $selection->$subSelector = new stdClass();
                }
                $selection = &$selection->$subSelector;
            } else {
                return null;
            }
        }
        return $selection = $value;
    } else {
        if (is_array($collection)) {
            return $collection[$key] = $value;
        } else {
            return $collection->$key = $value;
        }
    }
}

/**
 * Set a key to a value in a collection.
 *
 * Works with single keys or "dot" notation. If $key is an array, a simple
 * shallow array_merge is performed.
 *
 * @param string $key The key or property name of the value.
 * @param array &$collection The array or object to search.
 * @param mixed $value The value to set.
 * @return mixed Newly set value or if array merge
 * @deprecated Use {@link setvalr()}.
 */
function svalr($key, &$collection, $value = null) {
    setvalr($key, $collection, $value);
}