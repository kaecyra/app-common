<?php

/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @author Tim Gunter <tim@vanillaforums.com>
 * @copyright 2016 Tim Gunter
 * @license MIT
 */

namespace Kaecyra\AppCommon\Log;

use Interop\Container\ContainerInterface;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * A logger that can dispatch log events to many sub-loggers.
 *
 * @package app-common
 * @since 1.0
 */
class AggregateLogger extends BaseLogger implements ContainerInterface {

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
    public function addLogger(LoggerInterface $logger, $level = null, $key = null) {
        // Make a small attempt to prevent infinite cycles by disallowing all logger chaining.
        if ($logger instanceof AggregateLogger) {
            throw new \InvalidArgumentException("You cannot chain AggregateLoggers.", 500);
        }

        $level = $level ?? self::DEBUG;
        $key = $key ?? spl_object_hash($logger);
        $this->loggers[$key] = [
            'logger'    => $logger,
            'priority'  => static::levelPriority($level),
            'key'       => $key,
            'enabled'   => true
        ];
        return $this;
    }

    /**
     * Remove a logger by passing in its key.
     *
     * @param type $key
     * @param type $trigger
     * @return $this
     */
    public function removeLogger(string $key, $trigger = true) {
        if ($trigger && !$this->has($key)) {
            trigger_error("Logger $key was removed without being added.");
        }

        unset($this->loggers[$key]);

        return $this;
    }

    /**
     * Remove a logger by passing in its instance.
     *
     * @param LoggerInterface $logger
     * @param bool $trigger
     * @return AggregateLogger
     */
    public function removeLoggerByInstance($logger, $trigger = true) {
        foreach ($this->loggers as $key => $addedLogger) {
            if ($addedLogger[0] === $logger) {
                unset($this->loggers[$key]);
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
     * Disable a logger
     *
     * @param string $key
     */
    public function disableLogger($key) {
        if ($this->has($key)) {
            $this->loggers[$key]['enabled'] = false;
        }
    }

    /**
     * Enable a logger
     *
     * @param string $key
     */
    public function enableLogger($key) {
        if ($this->has($key)) {
            $this->loggers[$key]['enabled'] = true;
        }
    }

    /**
     * Get logger by key
     *
     * @param string $key
     * @return LoggerInterface
     */
    public function get($key) {
        return $this->loggers[$key] ?? new NullLogger;
    }

    /**
     * Check if logger exists
     *
     * @param string $key
     * @return boolean
     */
    public function has($key) {
        return array_key_exists($key, $this->loggers);
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

        foreach ($this->loggers as $logger) {
            /* @var LoggerInterface $logger */
            if (!$logger['enabled']) {
                continue;
            }

            if ($logger['priority'] >= $levelPriority) {
                try {
                    $logger['logger']->log($level, $message, $context);
                } catch (\Exception $ex) {
                    $inCall = false;
                    throw $ex;
                }
            }
        }

        $inCall = false;
    }

}
