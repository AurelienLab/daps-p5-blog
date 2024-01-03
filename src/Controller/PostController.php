<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Database\Query;
use App\Model\Post;

class PostController extends AbstractController
{

    public function index()
    {
        return $this->render('post/index.html.twig');
    }

    public function show()
    {
        return $this->render('post/show.html.twig');
    }
}
