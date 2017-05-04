<?php

/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2016 Vanilla Forums Inc.
 * @license MIT
 */

namespace Kaecyra\AppCommon\Log;

/**
 * A logger that writes using {@link syslog()}.
 *
 * @package app-common
 * @since 1.0
 */
class SyslogLogger extends BaseLogger {

    /**
     * Config
     * @var array
     */
    public $extra = [];

    public function __construct($workingDir, array $options = []) {
        $this->extra = $options;
        $this->extra['dir'] = $workingDir;

        $ident = $options['ident'] ?? 'com.kaecyra.app';
        $option = $options['syslog'] ?? LOG_ODELAY;
        $facility = $options['facility'] ?? LOG_LOCAL0;

        openlog($ident, $option, $facility);
    }

    public function __destruct() {
        closelog();
    }

    /**
     * Extract known columns and save the rest as attributes.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function log($level, $message, array $context = array()) {
        $realMessage = rtrim(static::interpolate($message, $context), "\n");

        if (isset($context['event'])) {
            $realMessage = sprintf("<%s> %s", $context['event'], $realMessage);
        }

        $realMessage = sprintf("[%s] %s\n", $level, $realMessage);

        syslog($level, $realMessage);
    }

}
