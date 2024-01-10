<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Router\Router;
use App\Repository\PostRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{


    public function index(): Response
    {
        $lastPost = PostRepository::getLastPublished(['category']);
        
        return $this->render(
            'homepage/index.html.twig',
            [
                'lastPost' => $lastPost
            ]
        );
    }
}
