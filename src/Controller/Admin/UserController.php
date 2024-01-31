<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Core\Exception\NotFoundException;
use App\Model\PostCategory;
use App\Repository\PostCategoryRepository;
use App\Repository\UserRepository;
use Behat\Transliterator\Transliterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends AbstractController
{

    /**
     * Users list
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(): Response
    {
        $users = UserRepository::getAll();

        return $this->render('Admin/user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function add(): Response
    {
        return $this->render('Admin/user/add.html.twig');
    }

    /**
     *
     * @param Request $request
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request): Response
    {
        $category = new PostCategory();

        if ($this->save($category, $request)) {
            return $this->redirect('admin.category.index');
        }

        return $this->render('Admin/category/add.html.twig');
    }

    /**
     * Display edit category form
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(int $id): Response
    {
        $category = PostCategoryRepository::getOrError($id);

        return $this->render(
            'Admin/category/edit.html.twig',
            [
                'category' => $category
            ]
        );
    }

    /**
     * Handle edit category form post
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function update(int $id, Request $request): Response
    {
        $category = PostCategoryRepository::getOrError($id);

        if ($this->save($category, $request)) {
            return $this->redirect('admin.category.index');
        }

        return $this->render(
            'Admin/category/edit.html.twig',
            [
                'category' => $category
            ]
        );
    }

    /**
     * Delete user
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws NotFoundException
     */
    public function remove(int $id)
    {
        $user = UserRepository::getOrError($id);

        UserRepository::remove($user);

        return $this->redirect('admin.user.index');
    }

    /**
     * Validate & save data from a form post
     *
     * @param PostCategory $postCategory
     * @param Request $request
     *
     * @return bool
     * @throws \Exception
     */
    private function save(PostCategory $postCategory, Request $request): bool|PostCategory
    {
        $data = $request->request;

        //Check CSRF Validity
        if (!$this->isCsrfValid('category_form', $data->get('_csrf'))) {
            throw new \Exception('Invalid CSRF token');
        }

        if (empty(trim($data->get('name')))) {
            $this->addFormError('name', 'Vous devez entrer un nom de catégorie');
        }

        if ($this->hasFormErrors()) {
            return false;
        }

        $slug = $data->get('slug');
        if (empty(trim($slug))) {
            $slug = $data->get('name');
        }

        $postCategory
            ->setName($data->get('name'))
            ->setSlug(Transliterator::urlize($slug));

        return PostCategoryRepository::save($postCategory);
    }
}
