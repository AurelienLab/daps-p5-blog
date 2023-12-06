<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Core\Router\Router;

class DashboardController extends AbstractController
{

    public function index()
    {
        return $this->render('Admin/dashboard/index.html.twig');
    }
}
