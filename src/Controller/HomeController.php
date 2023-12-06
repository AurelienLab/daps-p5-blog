<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Router\Router;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{


    public function index(): Response
    {
        return $this->render(
            'homepage/index.html.twig',
            [
                'date' => new DateTime()
            ]
        );
    }
}
