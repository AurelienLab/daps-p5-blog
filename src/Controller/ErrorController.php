<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{

    /**
     * Specific controller for error display on prod env (called from public/index.php)
     *
     * @param $code
     * @param $message
     * @param $displayMessage
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function error($code = 500, $message = '', $displayMessage = false)
    {
        if ($code == 0) {
            $code = 500;
        }

        $response = new Response();
        $response->setStatusCode($code);
        $response->setContent($this->twig->render('_error/default.html.twig', [
            'code' => $code,
            'message' => $message,
            'displayMessage' => $displayMessage
        ]));
        return $response;
    }


}
