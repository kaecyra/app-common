<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon\Event;

/**
 * Event binding convenience trait.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
trait EventBindsTrait {

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

}
