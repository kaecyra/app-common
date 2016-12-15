<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon;

/**
 * Config parser
 *
 * Abstracts config file parsing logic away from application logic. Handles JSON
 * configuration files, both physical and virtual.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
class Config {

    /**
     * Data store
     * @var Store
     */
    protected $store;

    /**
     * Config file
     * @var string
     */
    protected $file;

    /**
     * @var boolean
     */
    protected $writeable;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var boolean
     */
    protected $dirty;

    public function __construct($config) {
        $this->store = new Store;

        $this->type = $config['type'];
        switch ($this->type) {
            case 'file':
                $file = $config['file'];
                $this->writeable = $config['writeable'] ?? false;
                $new = !file_exists($file);
                $this->file = $file;
                $conf = '{}';
                if (!$new) {
                    $conf = file_get_contents($this->file);
                }

                $data = json_decode($conf, true);
                if (!$data) {
                    $data = [];
                }
                $this->store->prepare($data);
                $this->dirty = false;
                break;

            case 'virtual':
                $conf = $config['conf'];
                $this->writeable = false;
                $data = json_decode($conf, true);
                if (!$data) {
                    $data = [];
                }
                $this->store->prepare($data);
                $this->dirty = false;
                break;
        }
    }

    /**
     * Special config shortcut for application config
     *
     * @param string $file
     * @return Config
     */
    public static function app($file) {
        return self::file($file, true, true);
    }

    /**
     * Create config for physical file
     *
     * @param string $file
     * @param boolean $writeable
     * @param boolean $trigger
     * @return Config
     */
    public static function file($file, $writeable = false, $trigger = false) {
        $config = [
            'type' => 'file',
            'file' => $file,
            'writeable' => $writeable,
            'trigger' => $trigger
        ];
        return new Config($config);
    }

    /**
     * Create config for virtual file (text config)
     *
     * @param string $conf
     * @return Config
     */
    public static function virtual($conf) {
        $config = [
            'type' => 'virtual',
            'conf' => $conf
        ];
        return new Config($config);
    }

    /**
     * Get a config setting
     *
     * @param string $setting
     * @param mixed $default
     * @return mixed
     */
    public function get($setting = null, $default = null) {
        $setting = trim($setting);
        if (empty($setting)) {
            return $this->store->dump();
        }

        $value = $this->store->get($setting, $default);
        return self::parse($value);
    }

    /**
     * Save config
     *
     * @param string $setting
     * @param mixed $value
     */
    public function set($setting, $value = null) {
        $this->store->set($setting, $value);
        $this->dirty = true;
    }

    /**
     * Delete a key from the config
     *
     * @param string $setting
     */
    public function remove($setting) {
        $this->store->delete($setting);
        $this->dirty = true;
    }

    /**
     * Post-parse a returned value from the config
     *
     * Allows special meanings for things like 'on', 'off' and 'true' or 'false'.
     *
     * @param string $param
     * @return mixed
     */
    public static function parse($param) {
        if (!is_array($param) && !is_object($param)) {
            $compare = trim(strtolower($param));
            if (in_array($compare, array('yes', 'true', 'on', '1'))) {
                return true;
            }
            if (in_array($compare, array('no', 'false', 'off', '0'))) {
                return false;
            }
        }
        return $param;
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

    /**
     * Auto save on destruct
     */
    public function __destruct() {
        $this->save(null);
    }

}
