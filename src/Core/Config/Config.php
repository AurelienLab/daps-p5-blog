<?php

namespace App\Core\Config;

class Config
{
    private array $configFiles;
    private static $_instance;
    private $config;

    public function __construct(private readonly string $configFolder)
    {
        $directory = scandir($configFolder);
        $this->configFiles = array_diff($directory, array('..', '.'));
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private static function getInstance($directory = '')
    {
        if (is_null(self::$_instance)) {
            if (!empty($directory)) {
                self::$_instance = new Config($directory);
                self::$_instance->collectConfig();
            } else {
                throw new \Exception('Unable to create a new Config instance without folder path');
            }
        }

        return self::$_instance;
    }

    /**
     * @throws \Exception
     */
    private function collectConfig(): void
    {
        foreach ($this->configFiles as $file) {
            $fileName = basename($file, '.php');
            $fileConfig = require($this->configFolder . '/' . $file);

            if (!is_array($fileConfig)) {
                throw new \Exception(sprintf('Config file %s in %s is not an array', $file, $this->configFolder));
            }

            $this->config[$fileName] = $fileConfig;
        }
    }

    public static function load($directory): void
    {
        self::getInstance($directory);
    }

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