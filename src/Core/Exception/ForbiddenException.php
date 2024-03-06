<?php

namespace App\Core\Exception;

/**
 * Easily throw a 404 error
 */
class ForbiddenException extends \Exception
{


    public function __construct(string $message = '')
    {
        if (empty($message)) {
            $message = 'Unauthorized access.';
        }
        parent::__construct($message, 403);
    }


}
