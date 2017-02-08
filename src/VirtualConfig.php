<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon;

/**
 * File based config
 *
 * Handles virtual configuration files, both JSON strings and arrays.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
class VirtualConfig extends AbstractConfig {

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

        $this->type = 'virtual';
        $this->writeable = false;

        $data = $config['conf'];
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!is_array($data)) {
            $data = [];
        }

        $this->store->prepare($data);
        $this->dirty = false;
    }

    /**
     * Create config for virtual file (text config)
     *
     * @param string $conf
     * @return VirtualConfig
     */
    public static function load($conf) {
        $config = [
            'conf' => $conf
        ];
        return new FileConfig($config);
    }

    /**
     * Save config back to file
     *
     * @return boolean
     */
    public function save($force = false) {
        return true;
    }

}
