<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Core\Exception\NotFoundException;
use App\Model\Post;
use App\Repository\PostCategoryRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{

    /**
     * Categories list
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(): Response
    {
        $categories = PostRepository::getAll();

        return $this->render('Admin/post/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Display form to add a new post
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add(): Response
    {
        $categories = PostCategoryRepository::getAll();

        return $this->render(
            'Admin/post/add.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * Handle add form post
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function create(): Response
    {
        $request = Request::createFromGlobals();
        $post = new Post();

        if ($this->save($post, $request)) {
            return $this->redirect('admin.post.index');
        }

        return $this->render('Admin/post/add.html.twig');
    }

    /**
     * Display edit post form
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(int $id): Response
    {
        $post = PostRepository::getOrError($id);

        return $this->render(
            'Admin/post/edit.html.twig',
            [
                'post' => $post
            ]
        );
    }

    /**
     * Handle edit post form post
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function update(int $id): Response
    {
        $request = Request::createFromGlobals();
        $post = PostRepository::getOrError($id);

        if ($this->save($post, $request)) {
            return $this->redirect('admin.post.index');
        }

        return $this->render(
            'Admin/post/edit.html.twig',
            [
                'post' => $post
            ]
        );
    }

    /**
     * Delete post
     *
     * @param int $id
     *
     * @return RedirectResponse
     * @throws NotFoundException
     */
    public function remove(int $id)
    {
        $post = PostRepository::getOrError($id);

        PostRepository::remove($post);

        return $this->redirect('admin.post.index');
    }

    /**
     * Validate & save data from a form post
     *
     * @param Post $post
     * @param Request $request
     *
     * @return bool
     * @throws \Exception
     */
    private function save(Post $post, Request $request)
    {
        $data = $request->request;


        return true;
    }
}
