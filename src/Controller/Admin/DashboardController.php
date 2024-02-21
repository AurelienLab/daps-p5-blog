<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Core\Router\Router;

class DashboardController extends AbstractController
{

    /**
     * Admin dashboard
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        return $this->render('Admin/dashboard/index.html.twig');
    }
}
