<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Router\Router;
use DateTime;

class HomeController extends AbstractController
{


    public function index(): string
    {
        return $this->render(
            'homepage/index.html.twig',
            [
                'date' => new DateTime()
            ]
        );
    }
}
