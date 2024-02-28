<?php

namespace App\Controller\Admin;

use App\Core\Abstracts\AbstractController;
use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Core\Exception\NotFoundException;
use App\Core\Utils\Str;
use App\Model\Post;
use App\Model\PostCategory;
use App\Model\PostTag;
use App\Model\Tag;
use App\Repository\PostCategoryRepository;
use App\Repository\PostRepository;
use App\Repository\PostTagRepository;
use App\Repository\TagRepository;
use App\Validator\ChapoLengthValidator;
use App\Validator\DateTimeValidator;
use App\Validator\JsonValidator;
use App\Validator\NotEmptyValidator;
use App\Validator\NumericValidator;
use App\Validator\PostCategoryValidator;
use App\Validator\PostContentBlocksValidator;
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
        $posts = PostRepository::getAll(['category', 'user']);

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
    public function create(Request $request): Response
    {
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
        $post = PostRepository::getOrError($id, ['category', 'user']);
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
    public function update(int $id, Request $request): Response
    {
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

        // Validate form and return processed data
        $data = $this->validateForm($request, 'post_form', [
            'title' => [NotEmptyValidator::class],
            'category_id' => [PostCategoryValidator::class],
            'chapo' => [NotEmptyValidator::class, ChapoLengthValidator::class],
            'read_time' => [NotEmptyValidator::class, NumericValidator::class],
            'content' => [JsonValidator::class, PostContentBlocksValidator::class],
            'published_at' => [NotEmptyValidator::class, DateTimeValidator::class]
        ]);

        /* @var UploadedFile $featuredImage */
        $featuredImage = $request->files->get('featured_image');

        //Generate Slug
        $slug = $data->get('slug');
        if ($slug === null || empty(trim($slug))) {
            $slug = $data->get('title');
        }

        // Define post state
        $state = !empty($data->get('status'));

        // Handle cover image
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

        if (!empty($data->get('category_id'))) {
            $post->setCategory($data->get('category_id'));
        }

        $post
            ->setTitle(trim($data->get('title')))
            ->setSlug(Transliterator::urlize($slug))
            ->setChapo($data->get('chapo'))
            ->setReadTime($data->get('read_time'))
            ->setContent($data->get('content'))
            ->setPublishedAt($data->get('published_at'))
            ->setFeaturedImage($featuredImagePath)
            ->setStatus($state)
            ->setValidatedAt(new \DateTime())
            ->setValidatorUserId(1);


        if ($this->hasFormErrors()) {
            return false;
        }

        if ($post->getId() === null) {
            $post->setUser($this->getUser());
        }

        if ($uploadImage) {
            // Upload file
            $filename = Transliterator::urlize($slug).'-'.Str::rand(4);
            $filename .= '.'.$featuredImage->getClientOriginalExtension();
            $featuredImagePath = '/'.$featuredImage->move(config('uploads.post.featured_image.dir'), $filename);

            $post->setFeaturedImage($featuredImagePath);
        }

        $post = PostRepository::save($post);

        // Generate Tags
        $this->generateTags($data->get('tags'), $post);

        return true;
    }

    /**
     * Generate tag associations to post
     *
     * @param string $formData
     * @param Post $post
     *
     * @return void
     * @throws NotFoundException
     */
    private function generateTags(string $formData, Post $post)
    {
        $tags = json_decode($formData, true);

        $existingTags = [];
        foreach ($post->getTags() as $tag) {
            $existingTags[$tag->getId()] = $tag;
        }

        foreach ($tags as $tag) {
            if (isset($tag['id']) === true) {
                if (isset($existingTags[$tag['id']])) {
                    unset($existingTags[$tag['id']]);
                    continue;
                }

                $tagEntity = TagRepository::getOrError($tag['id']);
            } else {
                $tagEntity = new Tag();
                $tagEntity->setName($tag['name'])
                    ->setSlug(Transliterator::urlize($tag['name']));

                $tagEntity = TagRepository::save($tagEntity);
            }

            $relation = new PostTag();
            $relation->setPost($post)
                ->setTag($tagEntity);

            PostTagRepository::save($relation);
        }

        foreach ($existingTags as $tag) {
            $relation = PostTagRepository::getByPostAndTag($post, $tag);
            PostTagRepository::remove($relation);
        }
    }
}
