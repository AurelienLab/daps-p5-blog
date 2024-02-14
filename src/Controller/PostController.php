<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Core\Exception\NotFoundException;
use App\Repository\PostCategoryRepository;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{

    public function index(Request $request)
    {

        $filters = [];

        $category = $request->query->get('category');
        if (!is_null($category) && $category != 'all') {
            $category = PostCategoryRepository::findBySlug($category);
            if ($category) {
                $filters['category'] = $category->getId();
            }
        }

        $tag = $request->query->get('tag');
        if (!is_null($tag) && $tag != 'all') {
            $tag = TagRepository::getOneBySlug($tag);
            if ($tag) {
                $filters['tag'] = $tag->getId();
            }
        }

        $posts = PostRepository::getPublished(['category', 'user'], null, $filters);

        $categories = PostCategoryRepository::getAll();
        $categoryList = [];
        foreach ($categories as $category) {
            if (count($category->getPosts()) > 0) {
                $categoryList[] = $category;
            }
        }
        usort($categoryList, [$this, 'sortByEntityName']);

        $tags = TagRepository::getAll();
        $tagList = [];
        foreach ($tags as $tag) {
            if (count($tag->getPosts()) > 0) {
                $tagList[] = $tag;
            }
        }
        usort($tagList, [$this, 'sortByEntityName']);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'categoryList' => $categoryList,
            'tagList' => $tagList,
        ]);
    }

    public function show(string $slug)
    {
        $post = PostRepository::getOnePublishedBySlug($slug);

        if ($post == null) {
            throw new NotFoundException();
        }

        if (empty($post->getTags()->toArray())) {
            $relatedPosts = PostRepository::getPublished(['category', 'user'], 3);
        } else {
            $relatedPosts = PostRepository::getRelatedPosts($post);
        }

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

    private function sortByEntityName($a, $b)
    {
        return strcmp(trim(strtolower($a->getName())), trim(strtolower($b->getName())));
    }
}
