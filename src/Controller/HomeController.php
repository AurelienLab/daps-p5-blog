<?php

namespace App\Controller;


use App\Core\Abstracts\AbstractController;
use App\Core\Config\Config;

class HomeController extends AbstractController
{
    public function index(): string
    {
        return $this->render('homepage/index.html.twig', [
            'test' => ''
        ]);
    }

    public function test($id): string
    {
        return 'Hi ' . $id;
    }
}