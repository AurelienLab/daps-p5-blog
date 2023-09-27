<?php

namespace App\Core\Config;

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
     * @param string $configFolder
     */
    public function __construct(private readonly string $configFolder)
    {
        $directory = scandir($configFolder);
        $this->configFiles = array_diff($directory, ['..', '.']);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private static function getInstance($directory = '')
    {
        if (self::$_instance === null) {
            if (empty($directory) === false) {
                self::$_instance = new Config($directory);
                self::$_instance->collectConfig();
                return self::$_instance;
            }

            throw new \Exception('Unable to create a new Config instance without folder path');
        }

        return self::$_instance;
    }

    /**
     * @return void
     * @throws \Exception
     */
    private function collectConfig(): void
    {
        foreach ($this->configFiles as $file) {
            $fileName = basename($file, '.php');
            $filePath = $this->configFolder.'/'.$file;
            $fileConfig = require $filePath;

            if (is_array($fileConfig) === false) {
                throw new \Exception(sprintf('Config file %s in %s is not an array', $file, $this->configFolder));
            }

            $this->config[$fileName] = $fileConfig;
        }
    }


    /**
     * @param string $directory
     *
     * @return void
     * @throws \Exception
     */
    public static function load($directory): void
    {
        self::getInstance($directory);
    }

    /**
     * Get a config value by its dot notation key
     *
     * @param $value
     *
     * @return mixed
     * @throws \Exception
     */
    public static function get($value): mixed
    {
        $config = self::getInstance()->config;

        $breadcrumb = explode('.', $value);
        $cursor = $config;
        foreach ($breadcrumb as $needle) {
            if (is_array($cursor) && !isset($cursor[$needle])) {
                throw new \Exception(sprintf('Unable to find key %s in config.', $value));
            }

            $cursor = $cursor[$needle];
        }
        return $cursor;
    }
}
