<?php

namespace App\Core\Config;

use Exception;

class Config
{

    /**
     * @var array|false
     */
    private array $configFiles;

    /**
     * @var Config|null
     */
    private static $_instance;

    /**
     * @var array
     */
    private $config;


    /**
     * Loads config files and get config values
     *
     * @param string $configFolder
     */
    public function __construct(private readonly string $configFolder)
    {
        $directory = scandir($configFolder);
        $this->configFiles = array_diff($directory, ['..', '.']);
    }


    /**
     * Return singleton instance of class
     *
     * @param string $directory
     *
     * @return Config|null
     * @throws Exception
     */
    private static function getInstance(string $directory = ''): ?Config
    {
        if (self::$_instance === null) {
            if (empty($directory) === false) {
                self::$_instance = new Config($directory);
                self::$_instance->collectConfig();
                return self::$_instance;
            }

            throw new Exception('Unable to create a new Config instance without folder path');
        }

        return self::$_instance;
    }


    /**
     * Load all config files and collect them in $this->config
     *
     * @return void
     * @throws Exception
     */
    private function collectConfig(): void
    {
        foreach ($this->configFiles as $file) {
            $fileName = basename($file, '.php');
            $filePath = $this->configFolder.'/'.$file;
            $fileConfig = include $filePath;

            if (is_array($fileConfig) === false) {
                throw new Exception(sprintf('Config file %s in %s is not an array', $file, $this->configFolder));
            }

            $this->config[$fileName] = $fileConfig;
        }
    }


    /**
     * Create a new instance from directory or load existing instance
     *
     * @param string $directory
     *
     * @return void
     * @throws Exception
     */
    public static function load($directory): void
    {
        self::getInstance($directory);
    }


    /**
     * Get a config value by its dot notation key
     *
     * @param string $value Name of the value to retrieve from config
     *
     * @return mixed
     * @throws Exception
     */
    public static function get(string $value): mixed
    {
        $config = self::getInstance()->config;

        $breadcrumb = explode('.', $value);
        $cursor = $config;
        foreach ($breadcrumb as $needle) {
            if (is_array($cursor) === true && isset($cursor[$needle]) === false) {
                throw new Exception(sprintf('Unable to find key %s in config.', $value));
            }

            $cursor = $cursor[$needle];
        }
        return $cursor;
    }
}
