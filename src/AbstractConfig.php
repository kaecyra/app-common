<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon;

use Interop\Container\ContainerInterface;

/**
 * Abstract Config
 *
 * Base config handling functions including store interface.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
abstract class AbstractConfig implements ConfigInterface, ContainerInterface {

    /**
     * Data store
     * @var Store
     */
    protected $store;

    /**
     * @var boolean
     */
    protected $dirty;

    public function __construct() {
        $this->store = new Store;
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
     * Check if setting exists
     *
     * @param string $setting
     */
    public function has($setting): bool {
        return $this->store->has($setting);
    }

    /**
     * Delete a key from the config
     *
     * @param string $setting
     */
    public function remove($setting): bool {
        $this->dirty = true;
        return $this->store->delete($setting);
    }

    /**
     * Dump all settings from config
     *
     * @return array
     */
    public function dump() {
        return $this->store->dump();
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
     * Auto save on destruct
     */
    public function __destruct() {
        $this->save(null);
    }

}
