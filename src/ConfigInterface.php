<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon;

/**
 * Config Interface
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
interface ConfigInterface {

    public function get($setting, $default = null);

    public function set($setting, $value);

    public function remove($setting): bool;

    public function dump(): array;

    public function save($force = false);

}