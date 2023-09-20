<?php

namespace App\Core\Utils;

class Utils
{
    public static function loadHelpers($directory)
    {
        //Get file list and remove current & parent pointers
        $files = scandir($directory);
        $files = array_diff($files, array('..', '.'));

        //Load files
        foreach ($files as $file) {
            require_once($directory . '/' . $file);
        }
    }
}