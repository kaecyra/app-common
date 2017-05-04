<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon\Addon;

/**
 * Addon interface.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 * @since 1.0
 */
interface AddonInterface {

    /**
     * Set addon marker
     *
     * @param Addon $addon
     */
    public function setAddon(Addon $addon);

    /**
     * Addon startup
     *
     */
    public function start();

}