<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{


    public function error($code = 500, $message = '')
    {

        $response = new Response();
        $response->setStatusCode($code);
        $response->setContent($this->twig->render('_error/default.html.twig', [
            'code' => $code,
            'message' => $message
        ]));
        return $response;
    }
}
