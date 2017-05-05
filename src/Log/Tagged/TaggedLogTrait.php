<?php

/**
 * @license MIT
 * @copyright 2017 Tim Gunter
 */

namespace Kaecyra\AppCommon\Log\Tagged;

/**
 * Tagged log trait
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
trait TaggedLogTrait {

    /**
     * Log tag
     * @var string|Callable
     */
    protected $logTag = null;

    /**
     * Log tagged message
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function tLog(string $level, string $message, array $context = []) {
        $logtag = $this->getLogTag();
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        $this->log($level, "[{$logtag}] ".$message, $context);
    }

    /**
     * Get log tag
     *
     * @return string
     */
    protected function getLogTag() {
        return is_callable($this->logTag) ? call_user_func($this->logTag) : $this->logTag;
    }

    /**
     * Set log tag
     *
     * @param string|Callable $logTag
     */
    public function setLogTag($logTag) {
        $this->logTag = $logTag;
    }

    /**
     * Set default logtag callback
     *
     *
     */
    public function setDefaultLogCallback() {
        $this->setLogTag(function(){
            return (new \ReflectionClass($this))->getShortName();
        });
    }

}