<?php

/**
 * @author Tim Gunter <tim@vanillaforums.com>
 * @copyright 2016 Tim Gunter
 * @license MIT
 */

namespace Kaecyra\AppCommon\Log;

use Psr\Log\AbstractLogger;

/**
 * A logger that writes to the screen.
 */
class EchoLogger extends AbstractLogger {

    public function __construct($workingDir, $options = []) {
        //
    }

    /**
     * Extract known columns and save the rest as attributes.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function log($level, $message, array $context = []) {
        $realMessage = static::interpolate($message, $context);
        echo $realMessage;
    }

}
