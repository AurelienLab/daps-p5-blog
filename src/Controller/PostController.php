<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Repository\PostRepository;

class PostController extends AbstractController
{

    public function index()
    {
        $posts = PostRepository::getPublished(['category']);
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    public function show(string $slug)
    {
        $post = PostRepository::getOnePublishedBySlug($slug);

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }
}
