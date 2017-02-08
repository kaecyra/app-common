<?php

/**
 * @license MIT
 * @copyright 2016 Tim Gunter
 */

namespace Kaecyra\AppCommon;

/**
 * Config Collection
 *
 * Allows configuration to consist of multiple Config objects / files.
 *
 * @author Tim Gunter <tim@vanillaforums.com>
 * @package app-common
 */
class ConfigCollection extends AbstractConfig {

    /**
     * List of configs
     * @var array
     */
    protected $configs;

    public function __construct() {
        parent::__construct();

        $this->configs = new \SplObjectStorage();
    }

    /**
     * Add a ConfigInterface object to collection
     *
     * @param \Kaecyra\AppCommon\ConfigInterface $config
     * @return ConfigCollection
     */
    public function addConfig(ConfigInterface $config) {
        // Add to config list
        $this->configs->attach($config);

        // If collection is in merge mode, add config data to common store
        $this->store->merge($config->dump());
        return $this;
    }

    /**
     * Remove ConfigInterface object from collection
     *
     * @param \Kaecyra\AppCommon\ConfigInterface $config
     * @return ConfigCollection
     */
    public function removeConfig(ConfigInterface $config) {

        // If collection is in merge mode, don't permit removing configs (cannot unmerge)
        if ($this->merge) {
            throw new Exception("Cannot remove configs from merged collection.");
        }

        // Generate config object hash
        $hash = spl_object_hash($config);

        // Remove from config list
        unset($this->configs[$hash]);

        return $this;
    }

    /**
     * Add a physical config file
     *
     * @param string $file
     * @param bool $writeable
     * @return ConfigCollection
     */
    public function addFile($file, $writeable = false): ConfigCollection {
        $config = FileConfig::load($file, $writeable);
        if ($config) {
            $this->addConfig($config);
        }
        return $this;
    }

    /**
     * Add a virtual config
     *
     * @param string|array $data
     * @return ConfigCollection
     */
    public function addVirtual($data): ConfigCollection {
        $config = VirtualConfig::load($data);
        if ($config) {
            $this->addConfig($config);
        }
        return $this;
    }

    /**
     * Add a folder of config files
     *
     * @param string $folder
     * @param string $extension
     * @return ConfigCollection
     */
    public function addFolder($folder, $extension): ConfigCollection {
        $extLen = strlen($extension);
        $files = scandir($folder, SCANDIR_SORT_ASCENDING);
        foreach ($files as $file) {
            if (in_array($file, ['.','..'])) {
                continue;
            }
            if (substr($file, -$extLen) != $extension) {
                continue;
            }
            $config = FileConfig::load(paths($folder, $file), false);
            if ($config) {
                $this->addConfig($config);
            }
        }
        return $this;
    }

    /**
     * Save
     *
     * Cannot save a collection, so return false always.
     *
     * @param bool $force
     */
    public function save($force = false) {
        return false;
    }

}