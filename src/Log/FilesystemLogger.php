<?php

/**
 * @author Tim Gunter <tim@vanillaforums.com>
 * @copyright 2016 Tim Gunter
 * @license MIT
 */

namespace Kaecyra\AppCommon\Log;

/**
 * A logger that writes to the filesystem.
 *
 * @package app-common
 * @since 1.0
 */
class FilesystemLogger extends BaseLogger {

    /**
     * File path
     * @var string
     */
    private $file;

    /**
     * File pointer
     * @var Resource
     */
    private $fr;

    /**
     * Config
     * @var array
     */
    private $extra = [];

    public function __construct($workingDir, array $options = []) {
        $this->extra = $options;
        $this->extra['dir'] = $workingDir;

        $this->file = $this->extra['file'];
        if (substr($this->file, 0, 1) !== '/') {
            $this->file = rtrim($workingDir, '/') .'/'. $this->file;
        }
        $this->openLog();
    }

    public function __destruct() {
        $this->closeLog();
    }

    /**
     * Open log file for writing
     *
     * Also closes currently open log file if needed.
     *
     * @return void
     */
    public function openLog() {
        if ($this->file) {

            $logDir = dirname($this->file);
            if (!is_dir($logDir) || !file_exists($logDir)) {
                @mkdir($logDir, 0755, true);
            }

            if (!file_exists($this->file)) {
                $touched = touch($this->file);
                if (!$touched) {
                    throw new \Exception("Unable to open log file '{$this->file}', could not create");
                }
            }

            if (!is_writable($this->file)) {
                throw new \Exception("Unable to open log file '{$this->file}', not writable");
            }
            $this->fr = fopen($this->file, 'a');
        }
    }

    /**
     * Close file pointer
     */
    protected function closeLog() {
        fclose($this->fr);
    }

    /**
     * Rotate log file, closing and re-opening
     *
     * @throws \Exception
     */
    public function rotate() {
        $this->closeLog();
        $this->openLog();
    }

    /**
     * Extract known columns and save the rest as attributes.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function log($level, $message, array $context = []) {
        $realMessage = rtrim(static::interpolate($message, $context), "\n");
        $levelC = strtoupper(substr($level,0,1));
        $realMessage = sprintf("[ %1s ] %s\n", $levelC, $realMessage);

        fwrite($this->fr, $realMessage);
    }

}
