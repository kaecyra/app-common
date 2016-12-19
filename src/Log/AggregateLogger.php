<?php

/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @author Tim Gunter <tim@vanillaforums.com>
 * @copyright 2016 Tim Gunter
 * @license MIT
 */

namespace Kaecyra\AppCommon\Log;

use Psr\Log\LoggerInterface;

/**
 * A logger that can dispatch log events to many sub-loggers.
 *
 */
class AggregateLogger extends BaseLogger {

    /**
     * List of sub-loggers and their priorities
     * @var array
     */
    private $loggers = [];

    /**
     * Add a new logger to observe messages.
     *
     * @param LoggerInterface $logger
     * @param string $level
     * @return AggregateLogger
     */
    public function addLogger(LoggerInterface $logger, $level = null) {
        // Make a small attempt to prevent infinite cycles by disallowing all logger chaining.
        if ($logger instanceof AggregateLogger) {
            throw new \InvalidArgumentException("You cannot chain AggregateLoggers.", 500);
        }

        $level = $level ?? self::DEBUG;
        $this->loggers[] = [$logger, static::levelPriority($level)];
        return $this;
    }

    /**
     * Remove a logger that was previously added with {@link AggregateLogger::addLogger()}.
     *
     * @param LoggerInterface $logger
     * @param bool $trigger
     * @return AggregateLogger
     */
    public function removeLogger(LoggerInterface $logger, $trigger = true) {
        foreach ($this->loggers as $i => $addedLogger) {
            if ($addedLogger[0] === $logger) {
                unset($this->loggers[$i]);
                return $this;
            }
        }

        if ($trigger) {
            $class = get_class($logger);
            trigger_error("Logger $class was removed without being added.");
        }

        return $this;
    }

    /**
     * Log an event.
     *
     * @param string $event
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function event($event, $level, $message, $context = []) {
        $context['event'] = $event;
        $this->log($level, $message, $context);
    }

    /**
     * Log with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = []) {
        $levelPriority = self::levelPriority($level);
        if ($levelPriority > LOG_DEBUG) {
            throw new \Psr\Log\InvalidArgumentException("Invalid log level: $level.");
        }

        // Prevent an infinite cycle by setting an internal flag.
        static $inCall = false;
        if ($inCall) {
            return;
        }
        $inCall = true;

        foreach ($this->loggers as $row) {
            /* @var LoggerInterface $logger */
            list($logger, $loggerPriority) = $row;

            if ($loggerPriority >= $levelPriority) {
                try {
                    $logger->log($level, $message, $context);
                } catch (\Exception $ex) {
                    $inCall = false;
                    throw $ex;
                }
            }
        }

        $inCall = false;
    }

}
