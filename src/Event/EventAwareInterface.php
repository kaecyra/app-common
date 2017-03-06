<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon\Event;

/**
 * Event aware interface
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
interface EventAwareInterface {

    /**
     * Set event manager.
     *
     * @param EventManager $eventManager
     */
    public function setEventManager(EventManager $eventManager);

    /**
     * Get event manager.
     */
    public function getEventManager(): EventManager;

    /**
     * Register an event binding
     *
     * @param string $event
     * @param callable $handler
     * @return string|boolean:false
     */
    public function bind(string $event, callable $handler);

    /**
     * Fire an event
     *
     * @param string $event
     * @param array $arguments optional.
     */
    public function fire(string $event, array $arguments = null);

    /**
     * Fire an event from a sender object
     *
     * @param string $event
     * @param object $sender
     * @param array $arguments optional.
     */
    public function fireOff(string $event, $sender, array $arguments = null);

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
    public function fireFilter(string $event, $filter, array $arguments = null);

    /**
     * Fire event and collect the return values in an array
     *
     * EventType: Return
     * @param string $event
     */
    public function fireReturn(string $event);

    /**
     * Fire events and reflect on the handler to pass named arguments
     *
     * @param type $event
     * @param type $arguments
     */
    public function fireReflected(string $event, array $arguments = null);

}