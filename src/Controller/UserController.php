<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;

class UserController extends AbstractController
{

    public function subscribe()
    {
        return $this->render('user/subscribe.html.twig');
    }
}
