<?php

/**
 * @license MIT
 * @copyright 2016-2017 Tim Gunter
 */

namespace Kaecyra\AppCommon\Log\Tagged;

/**
 * Tagged log interface
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
interface TaggedLogInterface {

    public function tLog(string $level, string $message, array $context);

    public function setLogTag($logTag);

    public function setDefaultLogCallback();

}