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

    /**
     * List posts and filters
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(Request $request)
    {

        $filters = [];

        $category = $request->query->get('category');
        if ($category !== null && $category != 'all') {
            $category = PostCategoryRepository::findBySlug($category);
            if ($category) {
                $filters['category'] = $category->getId();
            }
        }

        $tag = $request->query->get('tag');
        if ($tag !== null && $tag != 'all') {
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

        $this->setTitle('Les articles');

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'categoryList' => $categoryList,
            'tagList' => $tagList,
        ]);
    }

    /**
     * Display post from slug
     *
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws NotFoundException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
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
        $this->setTitle($post->getTitle());
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'relatedPosts' => $relatedPosts
        ]);
    }

    /**
     * Used to sort tag or entity by name
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function sortByEntityName($a, $b)
    {
        return strcmp(trim(strtolower($a->getName())), trim(strtolower($b->getName())));
    }
}
