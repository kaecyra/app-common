<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon\Event;

/**
 * Event aware convenience trait.
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
     * Fire events
     *
     * @param string $event
     * @param mixed $arguments
     */
    public function fire($event, $arguments = null) {
        $this->getEventManager()->fire($event, $arguments);
    }

}
