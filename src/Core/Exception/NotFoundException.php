<?php

namespace App\Core\Exception;

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
