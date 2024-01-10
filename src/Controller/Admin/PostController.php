<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Core\Exception\NotFoundException;
use App\Core\Utils\Str;
use App\Model\Post;
use App\Model\PostCategory;
use App\Repository\PostCategoryRepository;
use App\Repository\PostRepository;
use Behat\Transliterator\Transliterator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController
{

    /**
     * posts list
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(): Response
    {
        $posts = PostRepository::getAll(['category']);

        return $this->render('Admin/post/index.html.twig', [
            'posts' => $posts
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

        $categories = PostCategoryRepository::getAll();
        return $this->render('Admin/post/add.html.twig', [
            'post' => $post,
            'categories' => $categories
        ]);
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
        $post = PostRepository::getOrError($id, ['category']);
        $categories = PostCategoryRepository::getAll();

        return $this->render(
            'Admin/post/edit.html.twig',
            [
                'post' => $post,
                'categories' => $categories
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

        $categories = PostCategoryRepository::getAll();
        return $this->render('Admin/post/edit.html.twig', [
            'post' => $post,
            'categories' => $categories
        ]);
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

        /** @var UploadedFile $featuredImage */
        $featuredImage = $request->files->get('featured_image');

        //Check CSRF Validity
        if (!$this->isCsrfValid('post_form', $data->get('_csrf'))) {
            throw new \Exception('Invalid CSRF token');
        }

        // Check title validity
        if (empty(trim($data->get('title')))) {
            $this->addFormError('title', 'Vous devez entrer un titre');
        }

        //Generate Slug
        $slug = $data->get('slug');
        if ($slug === null || empty(trim($slug))) {
            $slug = $data->get('title');
        }

        // Check category validity
        $category = null;
        if (!is_numeric($data->get('category_id'))) {
            $this->addFormError('category_id', 'Vous devez sélectionner une catégorie');
        } else {
            $category = PostCategoryRepository::get(intval($data->get('category_id')));
            if ($category === null) {
                $this->addFormError('category_id', 'Vous devez sélectionner une catégorie');
            }
        }

        // Check excerpt validity
        if (empty(trim($data->get('chapo')))
            || strlen(trim($data->get('chapo'))) < 50) {
            $this->addFormError('chapo', 'Le chapô doit faire au moins 50 caractères');
        }


        // Check read time validity
        if (!is_numeric($data->get('read_time'))) {
            $this->addFormError('read_time', 'Vous devez entrer un temps de lecture');
        }

        // Check content json validity
        $decodedContent = json_decode($data->get('content'), true);
        if ($decodedContent === null) {
            $this->addFormError('content', 'Une erreur est survenue lors de la récupération du contenu');
        }
        if (empty($decodedContent['blocks'])) {
            $this->addFormError('content', 'Vous devez entrer un contenu');
        }

        // Define post state
        $state = !empty($data->get('status'));

        $publishedAt = null;

        try {
            $publishedAt = new \DateTime($data->get('published_at'));
        } catch (\Exception) {
            $this->addFormError('published_at', 'Impossible d\'interpréter la date');
        }


        $featuredImagePath = $post->getFeaturedImage() ?? '';
        $uploadImage = false;
        if ($featuredImage == null && empty($post->getFeaturedImage()) == true) {
            $this->addFormError('featured_image', 'Aucune image détectée');
        } elseif ($featuredImage != null) {
            if (!str_starts_with($featuredImage->getMimeType(), 'image/')) {
                $this->addFormError('featured_image', 'Format incompatible');
            } else {
                $uploadImage = true;
            }
        }


        $post
            ->setTitle(trim($data->get('title')))
            ->setSlug(Transliterator::urlize($slug))
            ->setCategory($category)
            ->setChapo($data->get('chapo'))
            ->setReadTime($data->get('read_time'))
            ->setContent($data->get('content'))
            ->setPublishedAt($publishedAt)
            ->setFeaturedImage($featuredImagePath)
            ->setStatus($state)
            ->setUserId(1)
            ->setValidatedAt(new \DateTime())
            ->setValidatorUserId(1);


        if ($this->hasFormErrors()) {
            return false;
        }

        if ($uploadImage) {
            // Upload file
            $filename = Transliterator::urlize($slug).'-'.Str::rand(4);
            $filename .= '.'.$featuredImage->getClientOriginalExtension();
            $featuredImagePath = '/'.$featuredImage->move(config('uploads.post.featured_image.dir'), $filename);

            $post->setFeaturedImage($featuredImagePath);
        }

        PostRepository::save($post);
        return true;
    }
}
