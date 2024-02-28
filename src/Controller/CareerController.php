<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;

class CareerController extends AbstractController
{

    /**
     * Career page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $this->setTitle('Mon parcours');
        return $this->render('career/index.html.twig');
    }


}
