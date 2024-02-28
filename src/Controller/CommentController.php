<?php

namespace App\Controller;

use App\Core\Abstracts\AbstractController;
use App\Model\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Validator\NotEmptyValidator;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{


    /**
     * Handle comment post
     *
     * @param Request $request
     * @param int $postId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \App\Core\Exception\NotFoundException
     */
    public function postComment(Request $request, int $postId)
    {
        $post = PostRepository::getOrError($postId);

        $data = $this->validateForm($request, 'comment_form', [
            'content' => [NotEmptyValidator::class]
        ]);

        $comment = new Comment();

        $comment
            ->setUser($this->getUser())
            ->setPost($post)
            ->setContent($data->get('content'));

        CommentRepository::save($comment);

        return $this->redirect('articles.show', ['slug' => $post->getSlug()]);
    }


    /**
     * Handle admin comment edition
     *
     * @param Request $request
     * @param int $commentId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \App\Core\Exception\NotFoundException
     */
    public function editComment(Request $request, int $commentId)
    {
        $comment = CommentRepository::getOrError($commentId);

        $data = $this->validateForm($request, 'comment_form', [
            'content' => [NotEmptyValidator::class]
        ]);

        $comment
            ->setContent($data->get('content'));

        CommentRepository::save($comment);

        return $this->redirect('articles.show', ['slug' => $comment->getPost()->getSlug()]);
    }


    /**
     * Handle admin comment removal
     *
     * @param int $commentId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \App\Core\Exception\NotFoundException
     */
    public function removeComment(int $commentId)
    {
        $comment = CommentRepository::getOrError($commentId);

        CommentRepository::remove($comment);

        return $this->redirect('articles.show', ['slug' => $comment->getPost()->getSlug()]);
    }


}
