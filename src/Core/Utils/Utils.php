<?php

namespace App\Core\Utils;

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

        // Get file list and remove current & parent pointers.
        $files = scandir($directory);
        $files = array_diff($files, ['..', '.']);

        // Load files.
        foreach ($files as $file) {
            $filePath = $directory.'/'.$file;
            include_once $filePath;
        }
    }
}
