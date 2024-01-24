<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Exception\NotFoundException;
use App\Repository\PostRepository;
use App\Repository\TagRepository;

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

        if ($post == null) {
            throw new NotFoundException();
        }

        $relatedPosts = PostRepository::getRelatedPosts($post);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'relatedPosts' => $relatedPosts
        ]);
    }

    public function tag(string $slug)
    {
        $tag = TagRepository::getOneBySlug($slug);
        if ($tag == null) {
            throw new NotFoundException();
        }

        $posts = PostRepository::getPublishedByTag($tag);
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
