<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon\Event;

/**
 * Event aware trait
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
trait EventAwareTrait {

    /**
     * Event manager
     * @var EventManager
     */
    protected $eventManager;

    /**
     * Sets an event manager.
     *
     * @param EventManager $eventManager
     */
    public function setEventManager(EventManager $eventManager) {
        $this->eventManager = $eventManager;
    }

    /**
     * Get event manager.
     *
     * @return EventManager
     */
    public function getEventManager(): EventManager {
        return $this->eventManager;
    }

    /**
     * Bind to an event
     *
     * @param string $event
     * @param callable $handler
     * @return type
     */
    public function bind(string $event, callable $handler) {
        return $this->getEventManager()->bind($event, $handler);
    }

    /**
     * Fire an event
     *
     * @param string $event
     * @param mixed $arguments
     * @return type
     */
    public function fire(string $event, array $arguments = null) {
        return $this->getEventManager()->fire($event, $arguments);
    }

    /**
     * Fire an event from a sender object
     *
     * @param string $event
     * @param object $sender
     * @param array $arguments optional.
     */
    public function fireOff(string $event, $sender, array $arguments = null) {
        return $this->getEventManager()->fireOff($event, $sender, $arguments);
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
        return $this->getEventManager()->fireFilter($event, $filter, $arguments);
    }

    /**
     * Fire event and collect the return values in an array
     *
     * EventType: Return
     * @param string $event
     */
    public function fireReturn(string $event, array $arguments = null) {
        return $this->getEventManager()->fireReturn($event, $arguments);
    }

    /**
     * Fire events and reflect on the handler to pass named arguments
     *
     * @param type $event
     * @param type $arguments
     */
    public function fireReflected(string $event, array $arguments = null) {
        return $this->getEventManager()->fireReflected($event, $arguments);
    }
}