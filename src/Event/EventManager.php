<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon\Event;

/**
 * Lightweight event framework
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
class EventManager {

    /**
     * List of bindings
     * @var array
     */
    protected $bindings = [];

    /**
     * Whether we are ticking
     * @var boolean
     */
    protected $ticking = false;

    /**
     * We tick every 10 seconds by default
     * @var integer
     */
    protected $tickFreq = 10;
    protected $lastTick = 0;

    /**
     * Construct
     */
    public function __construct($tickFreq = 10) {
        $this->tickFreq = $tickFreq;
    }

    /**
     * Fire an event
     *
     * @param string $event
     * @param array $arguments optional.
     */
    public function fire(string $event, array $arguments = null) {
        $arguments = (array)$arguments;
        foreach ($this->getBindings($event) as $callback) {
            if (is_callable($callback)) {
                call_user_func_array($callback, $arguments);
            }
        }
    }

    /**
     * Fire an event from a sender object
     *
     * @param string $event
     * @param object $sender
     * @param array $arguments optional.
     */
    public function fireOff(string $event, $sender, array $arguments = null) {
        $arguments = (array)$arguments;
        array_unshift($arguments, $sender);
        return $this->fire($event, $arguments);
    }

    /**
     * Fire events in sequence
     *
     * Each callback receives the return value of the preceding callback as its
     * first argument.
     *
     * EventType: Filter
     * @param string $event
     * @param mixed $filter
     * @param array $arguments optional.
     */
    public function fireFilter(string $event, $filter, array $arguments = null) {
        $arguments = (array)$arguments;

        array_unshift($arguments, $filter);
        $filter = &$arguments[0];

        foreach ($this->getBindings($event) as $callback) {
            if (is_callable($callback)) {
                $arguments[0] = call_user_func_array($callback, $arguments);
            }
        }

        return $filter;
    }

    /**
     * Fire event and collect the return values in an array
     *
     * EventType: Return
     * @param string $event
     */
    public function fireReturn(string $event) {
        $return = [];
        $arguments = func_get_args();
        array_shift($arguments);

        foreach ($this->getBindings($event) as $callback) {
            if (is_callable($callback)) {
                $return[] = call_user_func_array($callback, $arguments);
            }
        }
        return $return;
    }

    /**
     *
     *
     * @param type $event
     * @param type $arguments
     */
    public function fireReflected(string $event, array $arguments = null) {
        foreach ($this->getBindings($event) as $callback) {
            $pass = [];
            if (is_string($callback) && is_callable($callback)) {
                $reflect = new ReflectionFunction($callback);
                $pass = !is_null($arguments) ? $this->reflect($reflect, $arguments) : null;
                $reflect->invokeArgs($pass);
            } elseif (is_array($callback) && is_callable($callback)) {
                $reflect = new ReflectionMethod($callback[0], $callback[1]);
                $pass = !is_null($arguments) ? $this->reflect($reflect, $arguments) : null;
                $reflectOn = is_string($callback[0]) ? null : $callback[0];
                $reflect->invokeArgs($reflectOn, $pass);
            } else {
                continue;
            }
        }
    }

    /**
     * Register an event binding
     *
     * @param string $event
     * @param callable $callback
     * @return string|boolean:false
     */
    public function bind(string $event, callable $callback) {
        // We can't bind to something that isn't callable
        if (!is_callable($callback)) {
            return false;
        }

        if (!isset($this->bindings[$event]) || !is_array($this->bindings[$event])) {
            $this->bindings[$event] = [];
        }

        // Check if this event is already registered
        $signature = $this->hash($callback);
        if (array_key_exists($signature, $this->bindings[$event])) {
            return $signature;
        }

        $this->bindings[$event][$signature] = $callback;
        return $signature;
    }

    /**
     * Get all bindings for an event
     *
     * @param string $event
     * @return array
     */
    protected function getBindings(string $event) {
        if (!isset($this->bindings[$event]) || !is_array($this->bindings[$event])) {
            return [];
        }
        return $this->bindings[$event];
    }

    /**
     * Remove an event binding
     *
     * @param string $event
     * @param string $signature
     * @return boolean successfully removed, or didn't exist
     */
    public function unbind(string $event, string $signature) {
        if (!isset($this->bindings[$event]) || !is_array($this->bindings[$event]) || !array_key_exists($signature, $this->bindings[$event])) {
            return false;
        }

        unset($this->bindings[$event][$signature]);
        return true;
    }

    /**
     * Reflect on the object or function and format an ordered arguement list
     *
     * @param Reflector $reflect
     * @param array $arguments
     */
    protected function reflect($reflect, array $arguments) {
        $pass = [];
        foreach ($reflect->getParameters() as $param) {
            if (isset($arguments[$param->getName()])) {
                $pass[] = $arguments[$param->getName()];
            } else {
                $pass[] = $param->getDefaultValue();
            }
        }
        return $pass;
    }

    /**
     * Get unique hash / id for callables
     *
     * @param callable $callback
     * @return string
     */
    public function hash(callable $callback) {

        // Global function calls
        if (is_string($callback)) {
            return 'string:'.$callback;
        }

        // Standardize to array callables
        if (is_object($callback)) {
            $callback = [$callback, ''];
        } else {
            $callback = (array)$callback;
        }

        // Callback to an instance method
        if (is_object($callback[0])) {

            return spl_object_hash($callback[0]) .'->'. $callback[1];

        // Callback is static
        } elseif (is_string($callback[0])) {

            return $callback[0] .'::'. $callback[1];

        }
    }

    /**
     * Enable periodic tick event
     *
     * @param integer $tickFreq optional
     * @return boolean
     */
    public function enableTicks(int $tickFreq = null) {

        if (is_null($tickFreq)) {
            $tickFreq = self::$tickFreq;
        }

        // Change ticking frequency
        self::$tickFreq = $tickFreq;
        self::$lastTick = microtime(true);

        // If we're already ticking, don't register again
        if (self::$ticking) {
            return true;
        }

        register_tick_function(['\Kaecyra\AppCommon\Event', 'tick']);
    }

    /**
     * Disable periodic tick event
     *
     */
    public function disableTicks() {
        if (self::$ticking) {
            unregister_tick_function(['\Kaecyra\AppCommon\Event', 'tick']);
        }
    }

    /**
     * Fire tick events every self::$tickFreq
     *
     */
    public function tick() {
        if ((microtime(true) - self::$lastTick) > self::$tickFreq) {
            self::$lastTick = microtime(true);
            $this->fire('tick');
        }
    }

}
