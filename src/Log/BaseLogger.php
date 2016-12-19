<?php

/**
 * @author Tim Gunter <tim@vanillaforums.com>
 * @copyright 2016 Tim Gunter
 * @license MIT
 */

namespace Kaecyra\AppCommon\Log;

use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;

/**
 * A logger helper trait to provide context replacement features.
 */
abstract class BaseLogger extends AbstractLogger {

    /**
     * Get the numeric priority for a log level.
     *
     * The priorities are set to the LOG_* constants from the {@link syslog()} function.
     * A lower number is more severe.
     *
     * @param string|int $level The string log level or an actual priority.
     * @return int Returns the numeric log level or `8` if the level is invalid.
     */
    public static function levelPriority($level) {
        static $priorities = [
            LogLevel::DEBUG     => LOG_DEBUG,
            LogLevel::INFO      => LOG_INFO,
            LogLevel::NOTICE    => LOG_NOTICE,
            LogLevel::WARNING   => LOG_WARNING,
            LogLevel::ERROR     => LOG_ERR,
            LogLevel::CRITICAL  => LOG_CRIT,
            LogLevel::ALERT     => LOG_ALERT,
            LogLevel::EMERGENCY => LOG_EMERG
        ];

        if (isset($priorities[$level])) {
            return $priorities[$level];
        } else {
            return LOG_DEBUG + 1;
        }
    }

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