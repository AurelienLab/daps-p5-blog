<?php

namespace App\Core\Utils;

use Symfony\Component\Finder\Finder;

class Utils
{


    /**
     * Load all functions in $directory into general scope
     *
     * @param string $directory Directory where utils functions are stored
     *
     * @return void
     */
    public static function loadHelpers(string $directory): void
    {
        $finder = new Finder();
        $finder->in($directory)->name('*.php')->sortByName();
        foreach ($finder as $file) {
            include_once $file->getPathname();
        }
    }
}
