<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon;

/**
 * File based config
 *
 * Handles JSON configuration files, both physical and virtual.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
class FileConfig extends AbstractConfig {

    /**
     * Config file
     * @var string
     */
    protected $file;

    /**
     * Whether the file is writeable
     * @var boolean
     */
    protected $writeable;

    public function __construct($config) {
        parent::__construct();

        $file = $config['file'];
        $this->writeable = $config['writeable'] ?? false;

        $this->file = $file;
        $data = '{}';
        if (file_exists($file)) {
            $data = file_get_contents($this->file);
        }

        $data = json_decode($data, true);
        if (!is_array($data)) {
            $data = [];
        }

        $this->store->prepare($data);
        $this->dirty = false;
    }

    /**
     * Special config shortcut for application config
     *
     * @param string $workingDir
     * @param string $file
     * @return FileConfig
     */
    public static function app($workingDir, $file) {
        $workingFile = rtrim($workingDir, '/').'/'.ltrim($file, '/');
        return self::file($workingFile, true, true);
    }

    /**
     * Create config for physical file
     *
     * @param string $file
     * @param boolean $writeable
     * @return FileConfig
     */
    public static function load($file, $writeable = false) {
        $config = [
            'file' => $file,
            'writeable' => $writeable
        ];
        return new FileConfig($config);
    }

    /**
     * Save config back to file
     *
     * @return boolean
     */
    public function save($force = false) {
        if (!$this->writeable) {
            return false;
        }

        if (!$this->dirty && !$force) {
            return null;
        }

        $savetype = $force ? 'forced ' : '';
        if (is_null($force)) {
            $savetype = 'auto ';
        }

        $path = dirname($this->file);
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $data = $this->store->dump();
        $conf = version_compare(PHP_VERSION, '5.4', '>=') ? json_encode($data, JSON_PRETTY_PRINT) : json_encode($data);
        unset($data);
        if (!$conf || !json_decode($conf)) {
            return;
        }
        $saved = (bool)$this->writeAtomic($this->file, $conf, 0755);

        if ($saved) {
            $this->dirty = false;
        }

        return $saved;
    }

    /**
     * Write file atomically
     *
     * @param string $filename
     * @param string $content
     * @param octal $mode
     * @return boolean
     */
    protected function writeAtomic($filename, $content, $mode) {
        $temp = tempnam(dirname($filename), 'atomic');

        if (!($fp = @fopen($temp, 'wb'))) {
            $temp = dirname($filename) . '/' . uniqid('atomic');
            if (!($fp = @fopen($temp, 'wb'))) {
                trigger_error(__METHOD__." : error writing temporary file '{$temp}'", E_USER_WARNING);
                return false;
            }
        }

        $br = fwrite($fp, $content);
        fclose($fp);
        if (!$br || $br != strlen($content)) {
            unlink($temp);
            return false;
        }

        chmod($temp, $mode);

        if (!rename($temp, $filename)) {
            unlink($filename);
            rename($temp, $filename);
        }
        return true;
    }

}
