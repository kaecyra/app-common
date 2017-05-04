<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon\Addon;

use Kaecyra\AppCommon\Log\LoggerBoilerTrait;

use Kaecyra\AppCommon\Event\EventManager;
use Kaecyra\AppCommon\Event\EventAwareInterface;
use Kaecyra\AppCommon\Event\EventAwareTrait;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Queue Worker Abstract addon base class
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 * @since 1.0
 */
abstract class AbstractAddon implements AddonInterface, EventAwareInterface, LoggerAwareInterface {

    use LoggerBoilerTrait;
    use LoggerAwareTrait;
    use EventAwareTrait;

    /**
     * Addon marker
     * @var Addon
     */
    protected $addon;

    /**
     * Addon config
     * @var array
     */
    protected $config;

    public function __construct($config = []) {
        $this->config = (array)$config;
    }

    /**
     * Set addon marker
     *
     * @param Addon $addon
     */
    public function setAddon(Addon $addon) {
        $this->addon = $addon;
    }

    /**
     * Do nothing
     */
    public function start() {

    }

}