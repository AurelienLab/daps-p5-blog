<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;

class ErrorController extends AbstractController
{
    public function error($code = 500, $message = '')
    {
        http_response_code($code);

        print($this->render('_error/default.html.twig', [
            'code' => $code,
            'message' => $message
        ]));
    }
}