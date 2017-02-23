<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon\Event;

/**
 * Event binds interface
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
interface EventBindsInterface {

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

}