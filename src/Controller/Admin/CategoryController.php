<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Core\Exception\NotFoundException;
use App\Model\PostCategory;
use App\Repository\PostCategoryRepository;
use Behat\Transliterator\Transliterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CategoryController extends AbstractController
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
        $categories = PostCategoryRepository::getAll();

        return $this->render('Admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * Display form to add a new category
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add(): Response
    {
        return $this->render('Admin/category/add.html.twig');
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function update(int $id): Response
    {
        $request = Request::createFromGlobals();
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
     * Delete category
     *
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws NotFoundException
     */
    public function remove(int $id)
    {
        $category = PostCategoryRepository::getOrError($id);

        PostCategoryRepository::remove($category);

        return $this->redirect('admin.category.index');
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
    private function save(PostCategory $postCategory, Request $request)
    {
        $data = $request->request;

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

        PostCategoryRepository::save($postCategory);

        return true;
    }
}