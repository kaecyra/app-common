<?php

/**
 * @author Tim Gunter <tim@vanillaforums.com>
 * @copyright 2016 Tim Gunter
 * @license MIT
 */

namespace Kaecyra\AppCommon\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * A trait that provides getLogger() and NullLogger passthru functionality.
 *
 */
trait LoggerBoilerTrait {

    /**
     * Get a logger
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger() {
        if (!($this->logger instanceof LoggerInterface)) {
            return new NullLogger;
        }
        return $this->logger;
    }

}