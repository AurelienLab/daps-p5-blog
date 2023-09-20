<?php

namespace App\Controller;


use App\Core\Abstracts\AbstractController;

class HomeController extends AbstractController
{
    public function index()
    {
        return $this->render('homepage/index.html.twig');
    }
}