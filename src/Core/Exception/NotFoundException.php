<?php

namespace App\Core\Exception;

/**
 * Easily throw a 404 error
 */
class NotFoundException extends \Exception
{


    public function __construct(string $message = '')
    {
        if (empty($message)) {
            $message = 'Not found.';
        }
        parent::__construct($message, 404);
    }


}
