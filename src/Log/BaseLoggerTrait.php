<?php

/**
 * @author Tim Gunter <tim@vanillaforums.com>
 * @copyright 2016 Tim Gunter
 * @license MIT
 */

namespace Kaecyra\AppCommon\Log;

/**
 * A logger helper trait to provide context replacement features.
 */
trait BaseLoggerTrait {

    /**
     * Interpolate contexts into messages containing bracket-wrapped format strings.
     *
     * @param string $format
     * @param array $context optional. array of key-value pairs to replace into the format.
     */
    public static function interpolate($format, $context = []) {
        $final = preg_replace_callback('/{([^\s][^}]+[^\s]?)}/', function ($matches) use ($context) {
            $field = trim($matches[1], '{}');
            if (array_key_exists($field, $context)) {
                return $context[$field];
            } else {
                return $matches[1];
            }
        }, $format);
        return $final;
    }

}