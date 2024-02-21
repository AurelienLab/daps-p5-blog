<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;

class CareerController extends AbstractController
{

    public function index()
    {
        $this->setTitle('Mon parcours');
        return $this->render('career/index.html.twig');
    }
}
