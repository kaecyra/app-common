<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon\Event;

/**
 * Event Firing Interface
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
     * Fire a simple event.
     *
     * @param string $event
     * @param mixed $arguments
     */
    public function fire($event, $arguments = null);

}